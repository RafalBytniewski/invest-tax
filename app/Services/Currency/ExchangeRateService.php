<?php

namespace App\Services\Currency;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getLastCurrencyPrice(string $symbol): ?float
    {
        $response = Http::get(
            sprintf(
                'https://api.nbp.pl/api/exchangerates/rates/a/%s/last/1/',
                strtoupper($symbol)
            ),
            ['format' => 'json']
        );

        if (! $response->ok()) {
            return null;
        }

        return data_get($response->json(), 'rates.0.mid');
    }

    public function getCurrencyPrice(string $symbol, string|CarbonInterface $date): ?float
    {
        $formattedDate = $date instanceof CarbonInterface ? $date->toDateString() : $date;

        $response = Http::get(
            sprintf(
                'https://api.nbp.pl/api/exchangerates/rates/a/%s/%s/',
                strtoupper($symbol),
                $formattedDate
            ),
            ['format' => 'json']
        );

        if (! $response->ok()) {
            return null;
        }

        return data_get($response->json(), 'rates.0.mid');
    }
}
