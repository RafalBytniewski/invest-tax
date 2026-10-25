<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getCurrencyPrice(string $symbol, string $date): ?float
    {
        $url = "https://api.nbp.pl/api/exchangerates/rates/a/{$symbol}/{$date}/?format=json";
        $response = Http::get($url);

        if ($response->ok()) {
            return (float) $response->json('rates.0.mid');
        }

        if (! $response->notFound() || $date !== now()->toDateString()) {
            return null;
        }

        $lastRateUrl = "https://api.nbp.pl/api/exchangerates/rates/a/{$symbol}/last/1/?format=json";
        $lastRateResponse = Http::get($lastRateUrl);

        return $lastRateResponse->ok()
            ? (float) $lastRateResponse->json('rates.0.mid')
            : null;
    }
}
