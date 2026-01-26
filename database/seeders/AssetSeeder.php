<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    // Crypto (zostawiamy jak było)
    Asset::create([
        'name' => 'Bitcoin',
        'image' => '',
        'symbol' => 'BTC',
        'asset_type' => 'crypto',
        'exchange_id' => null,
    ]);

    Asset::create([
        'name' => 'Ethereum',
        'image' => '',
        'symbol' => 'ETH',
        'asset_type' => 'crypto',
        'exchange_id' => null,
    ]);

    // GPW – akcje PL
    Asset::create([
        'name' => 'CD Projekt',
        'image' => '',
        'symbol' => 'CDR',
        'asset_type' => 'stock',
        'exchange_id' => 1,
    ]);

    Asset::create([
        'name' => 'PKN Orlen',
        'image' => '',
        'symbol' => 'PKN',
        'asset_type' => 'stock',
        'exchange_id' => 1,
    ]);

    Asset::create([
        'name' => 'PKO BP',
        'image' => '',
        'symbol' => 'PKO',
        'asset_type' => 'stock',
        'exchange_id' => 1,
    ]);

    Asset::create([
        'name' => 'KGHM',
        'image' => '',
        'symbol' => 'KGH',
        'asset_type' => 'stock',
        'exchange_id' => 1,
    ]);
}
}
