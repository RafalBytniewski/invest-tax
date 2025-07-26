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
        Asset::create([
            'name' => 'Bitcoin',
            'image' => '',
            'symbol' => 'BTC',
            'asset_type' => 'crypto',
            'exchange_id' => 1
        ]);
        Asset::create([
            'name' => 'Ethereum',
            'image' => '',
            'symbol' => 'ETH',
            'asset_type' => 'crypto',
            'exchange_id' => 1
        ]);
        Asset::create([
            'name' => 'Tron',
            'image' => '',
            'symbol' => 'TRX',
            'asset_type' => 'crypto',
            'exchange_id' => 1
        ]);
        Asset::create([
            'name' => 'Solana',
            'image' => '',
            'symbol' => 'SOL',
            'asset_type' => 'crypto',
            'exchange_id' => 1
        ]);
        Asset::create([
            'name' => 'XRP',
            'image' => '',
            'symbol' => 'XRP',
            'asset_type' => 'crypto',
            'exchange_id' => 1
        ]);
        Asset::create([
            'name' => 'CD Projekt',
            'image' => '',
            'symbol' => 'CDR',
            'asset_type' => 'stock',
            'exchange_id' => 3
        ]);
    }
}
