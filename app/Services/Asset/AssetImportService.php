<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetPrice;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

class AssetImportService
{
    private const BATCH_SIZE = 1000;

    public function import(
        string $filePath,
        string $type,
        bool $reset = false,
        ?callable $onProgress = null
    ): array {
        return DB::transaction(function () use ($filePath, $type, $reset, $onProgress) {
            $assetType = $this->mapAssetType($type);

            if ($reset) {
                $this->reset($assetType);
            }

            $assets = Asset::where('asset_type', $assetType)
                ->get()
                ->keyBy('symbol');

            $stats = [
                'new_assets' => 0,
                'existing_assets' => 0,
                'prices_processed' => 0,
                'assets_without_prices' => [],
                'assets_without_dividend' => 0,
                'dividend_processed' => 0,
            ];
            $currentAsset = 0;

            foreach ($this->readAssets($filePath) as $item) {
                $currentAsset++;
                $symbol = $item['symbol'] ?? null;
                if (!$symbol) {
                    throw new RuntimeException("Asset at position {$currentAsset} has no symbol.");
                }

                $asset = $assets->get($symbol);
                if (!$asset) {
                    $asset = new Asset([
                        'symbol' => $symbol,
                        'asset_type' => $assetType,
                        'name' => $item['name'] ?? $symbol,
                        'exchange_id' => $this->mapExchange($item['exchange'] ?? null),
                    ]);
                    $stats['new_assets']++;
                } else {
                    $stats['existing_assets']++;
                }

                if (is_null($asset->sector) && !empty($item['sector'])) {
                    $asset->sector = $item['sector'];
                }
                if (is_null($asset->industry) && !empty($item['industry'])) {
                    $asset->industry = $item['industry'];
                }
                if (empty($asset->name) && !empty($item['name'])) {
                    $asset->name = $item['name'];
                }
                $asset->save();
                $assets->put($symbol, $asset);

                $prices = $item['prices'] ?? [];
                if (empty($prices)) {
                    $stats['assets_without_prices'][] = $symbol;
                }
                $priceRows = [];
                foreach ($prices as $price) {
                    $priceRows[] = [
                        'asset_id' => $asset->id,
                        'date' => $price['date'],
                        'source' => 'json_import',
                        'close_price' => $price['close'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if (count($priceRows) >= self::BATCH_SIZE) {
                        AssetPrice::upsert($priceRows, ['asset_id', 'date', 'source'], ['close_price', 'updated_at']);
                        $priceRows = [];
                    }
                    $stats['prices_processed']++;
                }
                if ($priceRows) {
                    AssetPrice::upsert($priceRows, ['asset_id', 'date', 'source'], ['close_price', 'updated_at']);
                }

                $dividends = $item['dividends'] ?? [];
                if (empty($dividends)) {
                    $stats['assets_without_dividend']++;
                }
                foreach (array_chunk($dividends, self::BATCH_SIZE) as $batch) {
                    $this->insertMissingDividends($asset->id, $batch, $stats);
                }

                if ($onProgress) {
                    $onProgress($currentAsset, $currentAsset, $symbol);
                }
            }

            return $stats;
        });
    }

    /** Read one top-level asset object at a time, keeping the existing JSON format. */
    private function readAssets(string $filePath): \Generator
    {
        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new RuntimeException("Unable to open import file: {$filePath}");
        }

        try {
            $inArray = false;
            $inString = false;
            $escaped = false;
            $depth = 0;
            $object = '';
            $started = false;

            while (!feof($handle)) {
                $chunk = fread($handle, 1024 * 1024);
                if ($chunk === false) {
                    throw new RuntimeException('Unable to read import file.');
                }

                $length = strlen($chunk);
                for ($i = 0; $i < $length; $i++) {
                    $char = $chunk[$i];
                    if (!$inArray) {
                        if (ctype_space($char)) {
                            continue;
                        }
                        if ($char !== '[') {
                            throw new RuntimeException('Import JSON must contain a top-level array.');
                        }
                        $inArray = true;
                        continue;
                    }

                    if ($depth === 0) {
                        if (ctype_space($char) || $char === ',') {
                            continue;
                        }
                        if ($char === ']') {
                            return;
                        }
                        if ($char !== '{') {
                            throw new RuntimeException('Each top-level JSON item must be an object.');
                        }
                        $started = true;
                        $depth = 1;
                        $object = '{';
                        continue;
                    }

                    $object .= $char;
                    if ($inString) {
                        if ($escaped) {
                            $escaped = false;
                        } elseif ($char === '\\') {
                            $escaped = true;
                        } elseif ($char === '"') {
                            $inString = false;
                        }
                        continue;
                    }

                    if ($char === '"') {
                        $inString = true;
                    } elseif ($char === '{' || $char === '[') {
                        $depth++;
                    } elseif ($char === '}' || $char === ']') {
                        $depth--;
                    }

                    if ($depth === 0) {
                        try {
                            $item = json_decode($object, true, 512, JSON_THROW_ON_ERROR);
                        } catch (JsonException $e) {
                            throw new RuntimeException('Invalid JSON asset record: ' . $e->getMessage(), 0, $e);
                        }
                        yield $item;
                        $object = '';
                        $started = false;
                    }
                }
            }

            if (!$inArray || $started || $depth !== 0) {
                throw new RuntimeException('Unexpected end of import JSON.');
            }
        } finally {
            fclose($handle);
        }
    }

    private function insertMissingDividends(int $assetId, array $dividends, array &$stats): void
    {
        $stats['dividend_processed'] += count($dividends);
        $keys = [];
        $rows = [];
        foreach ($dividends as $dividend) {
            $date = $dividend['date'];
            $amount = $dividend['amount'];
            $key = $date . "\0" . $amount;
            if (isset($keys[$key])) {
                continue;
            }
            $keys[$key] = true;
            $rows[$key] = [
                'asset_id' => $assetId,
                'ex_date' => $date,
                'amount' => $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $existing = DB::table('dividends')
            ->where('asset_id', $assetId)
            ->whereIn('ex_date', array_values(array_unique(array_column($rows, 'ex_date'))))
            ->get(['ex_date', 'amount']);

        foreach ($existing as $row) {
            unset($rows[$row->ex_date . "\0" . $row->amount]);
        }

        foreach (array_chunk(array_values($rows), self::BATCH_SIZE) as $batch) {
            DB::table('dividends')->insert($batch);
        }
    }

    public function reset(string $assetType): void
    {
        $assetIds = Asset::where('asset_type', $assetType)->pluck('id');
        AssetPrice::whereIn('asset_id', $assetIds)->delete();
        DB::table('dividends')->whereIn('asset_id', $assetIds)->delete();
        Asset::where('asset_type', $assetType)->delete();
    }

    private function mapAssetType(string $type): string
    {
        return match ($type) {
            'us-stock', 'eu-stock', 'pl-stock' => 'stock',
            'etf' => 'etf',
            'crypto' => 'crypto',
            default => $type,
        };
    }

    private function mapExchange(?string $exchange): ?int
    {
        if (!$exchange) {
            return null;
        }
        return match (strtoupper(trim($exchange))) {
            'GPW' => 2,
            'NASDAQ' => 3,
            'XETRA' => 4,
            'NYSE' => 5,
            'XPAR' => 6,
            default => null,
        };
    }
}