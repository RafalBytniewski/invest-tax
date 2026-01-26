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
            'name' => 'Giełda Papierów Wartościowych',
            'symbol' => 'GPW',
            'country' => 'Poland',
            'currency' => 'PLN',
            'url' => 'https://www.gpw.pl/',
            'timezone' => 'Europe/Warsaw',
            'trading_hours' => '9-17'
        ]);
    }
}
