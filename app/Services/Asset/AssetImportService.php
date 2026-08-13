<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetPrice;
use Illuminate\Support\Facades\DB;

class AssetImportService
{
    public function import(
        string $filePath,
        string $type,
        bool $reset = false,
        ?callable $onProgress = null
    ): array {
        return DB::transaction(function () use (
            $filePath,
            $type,
            $reset,
            $onProgress
        ) {
            $assetType = $this->mapAssetType($type);

            if ($reset) {
                $this->reset($assetType);
            }

            $json = json_decode(
                file_get_contents($filePath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $totalAssets = count($json);
            $currentAsset = 0;

            $newAssets = 0;
            $existingAssets = 0;
            $pricesProcessed = 0;
            $assetsWithoutPrices = [];

            foreach ($json as $item) {
                $currentAsset++;

                $asset = Asset::firstOrNew([
                    'symbol' => $item['symbol'],
                    'asset_type' => $assetType,
                ]);

                if (!$asset->exists) {
                    $newAssets++;

                    $asset->name = $item['name'];
                    $asset->exchange_id = $this->mapExchange(
                        $item['exchange'] ?? null
                    );
                } else {
                    $existingAssets++;
                }

                // Update only when null in DB
                if (is_null($asset->sector) && !empty($item['sector'])) {
                    $asset->sector = $item['sector'];
                }

                if (is_null($asset->industry) && !empty($item['industry'])) {
                    $asset->industry = $item['industry'];
                }

                // Update name if empty
                if (empty($asset->name) && !empty($item['name'])) {
                    $asset->name = $item['name'];
                }

                $asset->save();

                $prices = $item['prices'] ?? [];

                if (empty($prices)) {
                    $assetsWithoutPrices[] = $asset->symbol;
                }

                foreach ($prices as $price) {
                    AssetPrice::updateOrCreate(
                        [
                            'asset_id' => $asset->id,
                            'date' => $price['date'],
                            'source' => 'json_import',
                        ],
                        [
                            'close_price' => $price['close'],
                        ]
                    );

                    $pricesProcessed++;
                }

                if ($onProgress) {
                    $onProgress(
                        $currentAsset,
                        $totalAssets,
                        $asset->symbol
                    );
                }
            }

            return [
                'new_assets' => $newAssets,
                'existing_assets' => $existingAssets,
                'prices_processed' => $pricesProcessed,
                'assets_without_prices' => $assetsWithoutPrices,
            ];
        });
    }

    public function reset(string $assetType): void
    {
        $assetIds = Asset::where('asset_type', $assetType)->pluck('id');

        AssetPrice::whereIn('asset_id', $assetIds)->delete();

        Asset::where('asset_type', $assetType)->delete();
    }

    private function mapAssetType(string $type): string
    {
        return match ($type) {
            'us-stock',
            'eu-stock',
            'pl-stock' => 'stock',

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

        $exchange = strtoupper(trim($exchange));

        return match ($exchange) {
            'GPW' => 2,
            'NASDAQ' => 3,
            'XETRA' => 4,
            'NYSE' => 5,
            'XPAR' => 6,
            default => null,
        };
    }
}