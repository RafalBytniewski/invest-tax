<?php

namespace Database\Seeders;

use App\Models\Exchange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exchange::create([
            'name' => 'Binance',
            'type' => 'crypto_exchange',
            'image' => '',
            'url' => 'https://bitcoin.com',
            'country' => NULL,
            'currency' => NULL,
        ]);
        Exchange::create([
            'name' => 'Coinbase',
            'type' => 'crypto_exchange',
            'image' => '',
            'url' => 'https://coinbase.com',
            'country' => NULL,
            'currency' => NULL,
        ]);
        Exchange::create([
            'name' => 'XTB',
            'type' => 'broker',
            'image' => '',
            'url' => 'https://xtb.com',
            'country' => NULL,
            'currency' => NULL,
        ]);
    }
}
