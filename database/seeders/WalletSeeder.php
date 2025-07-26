<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wallet::create([
            'name' => 'Crypto',
            'description' => 'Crypto assets',
            'user_id' => 1,
            'exchange_id' => 1
        ]);
    }
}
