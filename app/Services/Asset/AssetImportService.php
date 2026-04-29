<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetPrice;
use Illuminate\Support\Facades\DB;

class AssetImportService
{
    public function import(string $filePath, string $type): void
    {
        DB::transaction(function () use ($filePath, $type) {

            $json = json_decode(file_get_contents($filePath), true);

            foreach ($json as $item) {

                $asset = Asset::firstOrNew([
                    'symbol' => $item['symbol']
                ]);

                // zawsze ustaw jeśli nowy rekord
                if (!$asset->exists) {
                    $asset->name = $item['name'];
                    $asset->asset_type = $type;
                    $asset->exchange_id = $this->mapExchange($item['exchange'] ?? null);
                }

                // update tylko gdy null w DB
                if (is_null($asset->sector) && !empty($item['sector'])) {
                    $asset->sector = $item['sector'];
                }

                if (is_null($asset->industry) && !empty($item['industry'])) {
                    $asset->industry = $item['industry'];
                }

                // opcjonalnie update name jeśli pusty
                if (empty($asset->name) && !empty($item['name'])) {
                    $asset->name = $item['name'];
                }

                $asset->save();

                foreach ($item['prices'] as $price) {

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
                }
            }
        });
    }

    public function reset(string $type): void
    {
        DB::transaction(function () use ($type) {

            $assetIds = Asset::where('asset_type', $type)->pluck('id');

            AssetPrice::whereIn('asset_id', $assetIds)->delete();

            Asset::where('asset_type', $type)->delete();
        });
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
            default => null
        };
    }
}
