<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $transactions = [
        // CD Projekt (asset_id = 3)
        [
            'type' => 'buy',
            'asset_id' => 3,
            'quantity' => 10,
            'price' => 120.50,
            'date' => '2021-06-15',
        ],
        [
            'type' => 'sell',
            'asset_id' => 3,
            'quantity' => 4,
            'price' => 155.00,
            'date' => '2023-09-20',
        ],

        // PKN Orlen (asset_id = 4)
        [
            'type' => 'buy',
            'asset_id' => 4,
            'quantity' => 20,
            'price' => 68.30,
            'date' => '2022-03-10',
        ],
        [
            'type' => 'sell',
            'asset_id' => 4,
            'quantity' => 10,
            'price' => 72.90,
            'date' => '2024-01-12',
        ],

        // PKO BP (asset_id = 5)
        [
            'type' => 'buy',
            'asset_id' => 5,
            'quantity' => 50,
            'price' => 31.20,
            'date' => '2021-11-05',
        ],

        // KGHM (asset_id = 6)
        [
            'type' => 'buy',
            'asset_id' => 6,
            'quantity' => 15,
            'price' => 142.00,
            'date' => '2022-07-18',
        ],
        [
            'type' => 'sell',
            'asset_id' => 6,
            'quantity' => 5,
            'price' => 165.50,
            'date' => '2024-05-08',
        ],
    ];

    foreach ($transactions as $data) {
        $fees = round($data['quantity'] * $data['price'] * 0.001, 2); // 0.1% prowizji
        $totalValue = ($data['quantity'] * $data['price']) + $fees;

        Transaction::create([
            'type'           => $data['type'],
            'reward_type'    => null,
            'currency'       => 'PLN',
            'quantity'       => $data['quantity'],
            'price_per_unit' => $data['price'],
            'total_fees'     => $fees,
            'total_value'    => $totalValue,
            'date'           => $data['date'],
            'notes'          => 'Seeder GPW demo',
            'wallet_id'      => 1,
            'asset_id'       => $data['asset_id'],
        ]);
    }
}
}
