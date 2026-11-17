<?php

namespace App\Services\Currency;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NbpExchangeRateProvider implements ExchangeRateProvider
{
    /**
     * @return list<array{date: string, rate: float}>
     */
    public function rates(string $currency, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $currency = strtolower($currency);

        $response = Http::baseUrl(config('services.nbp.base_url'))
            ->acceptJson()
            ->connectTimeout(3)
            ->timeout(10)
            ->retry([100, 500, 1000], 0, throw: false)
            ->get("/api/exchangerates/rates/a/{$currency}/{$from->toDateString()}/{$to->toDateString()}/");

        if ($response->failed()) {
            throw new RuntimeException('NBP exchange rate request failed with status '.$response->status().'.');
        }

        $rates = $response->json('rates');

        if (! is_array($rates)) {
            throw new RuntimeException('NBP exchange rate response has an invalid format.');
        }

        return collect($rates)
            ->filter(fn (mixed $rate): bool => is_array($rate) && isset($rate['effectiveDate'], $rate['mid']))
            ->map(fn (array $rate): array => [
                'date' => (string) $rate['effectiveDate'],
                'rate' => (float) $rate['mid'],
            ])
            ->values()
            ->all();
    }
}
