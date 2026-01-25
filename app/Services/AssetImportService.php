<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetPrice;
use Illuminate\Support\Facades\DB;

class AssetImportService
{
    public function import(string $filePath): void
    {
        DB::transaction(function () use ($filePath) {

            $json = json_decode(file_get_contents($filePath), true);

            $total = count($json);
            $current = 0;

            foreach ($json as $item) {
                $current++;

                // Exchange (nullable)
                $exchangeId = $this->mapExchange($item['exchange'] ?? null, $item['Symbol']);

                // 1️⃣ Asset
                $asset = Asset::updateOrCreate(
                    ['symbol' => $item['Symbol']],
                    [
                        'name' => $item['Security'] ?? $item['Symbol'],
                        'asset_type' => 'stock',
                        'exchange_id' => $exchangeId, // może być null
                    ]
                );

                // 2️⃣ Prices (opcjonalne)
                if (empty($item['prices']) || !is_array($item['prices'])) {
                    echo "⚠️  Brak prices dla {$item['Symbol']} — pomijam ceny\n";
                } else {
                    foreach ($item['prices'] as $price) {
                        if (!isset($price['date'], $price['close'])) {
                            echo "⚠️  Nieprawidłowy rekord ceny dla {$item['Symbol']}\n";
                            continue;
                        }

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

                // 📊 Progress
                echo "✅ Zapisano {$current}/{$total} ({$item['Symbol']})\n";
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

    private function mapExchange(?string $exchange, string $symbol): ?int
    {
        if (!$exchange) {
            echo "⚠️  Brak exchange dla {$symbol}\n";
            return null;
        }

        return match (strtoupper($exchange)) {
            'NYSE' => 5,
            'NASDAQ' => 3,
            default => function () use ($exchange, $symbol) {
                echo "⚠️  Nieznana giełda '{$exchange}' dla {$symbol} — zapis bez exchange_id\n";
                return null;
            },
        };
    }
}
