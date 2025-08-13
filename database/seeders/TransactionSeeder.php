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
        ['type' => 'buy',  'quantity' => 12,     'price' => 400000, 'asset_id' => 1],
        ['type' => 'buy',  'quantity' => 2,      'price' => 9000,   'asset_id' => 2],
        ['type' => 'buy',  'quantity' => 1123,   'price' => 0.9,    'asset_id' => 3],
        ['type' => 'buy',  'quantity' => 11,     'price' => 777,    'asset_id' => 4],
        ['type' => 'buy',  'quantity' => 1321,   'price' => 1,      'asset_id' => 3],
        ['type' => 'buy',  'quantity' => 121,    'price' => 8888,   'asset_id' => 2],
        ['type' => 'buy',  'quantity' => 0.001,  'price' => 400000, 'asset_id' => 1],
        ['type' => 'sell', 'quantity' => -2,      'price' => 777,    'asset_id' => 4],
        ['type' => 'buy',  'quantity' => 0.5,    'price' => 8787,   'asset_id' => 2],
        ['type' => 'sell', 'quantity' => -1.1,    'price' => 8787,   'asset_id' => 2],
        ['type' => 'sell', 'quantity' => -0.02,   'price' => 400000, 'asset_id' => 1],
    ];

    $dates = [
        '2024-11-21', '2024-10-10', '2021-08-04', '2022-10-10', '2022-10-10',
        '2022-10-10', '2025-05-10', '2022-10-10', '2021-02-13', '2023-09-23', '2024-01-10'
    ];

    foreach ($transactions as $index => $data) {
        $quantity = $data['quantity'];
        $price = $data['price'];
        $fees = 0;

        Transaction::create([
            'type'            => $data['type'],
            'reward_type'     => null,
            'currency'        => 'PLN',
            'quantity'        => $quantity,
            'price_per_unit'  => $price,
            'total_fees'      => $fees,
            'total_value'     => ($quantity * $price) + $fees,
            'date'            => $dates[$index],
            'notes'           => 'test data',
            'wallet_id'       => 1,
            'asset_id'        => $data['asset_id'],
        ]);
    }
}

}
