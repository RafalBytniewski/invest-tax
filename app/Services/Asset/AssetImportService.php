<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetPrice;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AssetImportService
{
    public function import(string $filePath): void
    {
        DB::transaction(function () use ($filePath) {

            $json = json_decode(file_get_contents($filePath), true);

            foreach ($json as $item) {

                $asset = Asset::updateOrCreate(
                    ['symbol' => $item['symbol']],
                    [
                        'name' => $item['name'],
                        'sector' => $item['sector'],
                        'industry' => $item['industry'],
                        'asset_type' => 'stock',
                        'exchange_id' => $this->mapExchange($item['exchange']),
                    ]
                );

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

    public function reset(): void
    {
        DB::transaction(function () {
            AssetPrice::where('source', 'json_import')->delete();
            Asset::where('asset_type', 'stock')->delete();
        });
    }

    private function mapExchange(?string $exchange): int
    {
        $exchange = strtoupper(trim($exchange));

        return match ($exchange) {
            'GPW' => 2,
            'NASDAQ' => 3,
            'XETRA' => 4,            
            'NYSE' => 5,
            'XPAR' => 6,
            default => throw new InvalidArgumentException("Invalid exchange: {$exchange}")
        };
    }
}