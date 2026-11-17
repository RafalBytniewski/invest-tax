<?php

namespace App\Services\Currency;

use App\Models\ExchangeRate;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class ExchangeRateService
{
    public function __construct(private readonly ExchangeRateProvider $provider) {}

    /**
     * @return array{requested: int, stored: int}
     */
    public function sync(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $stored = 0;
        $requested = 0;
        $periodStart = $from;
        $currencies = ['EUR', 'USD'];

        while ($periodStart->lessThanOrEqualTo($to)) {
            $periodEnd = $periodStart->addDays(92)->min($to);
            $requested += $periodEnd->diffInDays($periodStart) + 1;

            foreach ($currencies as $currency) {
                $rates = $this->provider->rates($currency, $periodStart, $periodEnd);

                foreach ($rates as $rate) {
                    $baseRate = $rate['rate'];

                    ExchangeRate::query()->upsert([
                        $this->record($currency, 'PLN', $rate['date'], $baseRate),
                        $this->record('PLN', $currency, $rate['date'], 1 / $baseRate),
                    ], ['base_currency', 'quote_currency', 'date', 'source'], ['rate', 'updated_at']);

                    $stored += 2;
                }
            }

            $periodStart = $periodEnd->addDay();
        }

        return compact('requested', 'stored');
    }

    public function getRate(string $baseCurrency, string $quoteCurrency, CarbonInterface $date): ?string
    {
        if (strtoupper($baseCurrency) === strtoupper($quoteCurrency)) {
            return '1.0000000000';
        }

        return ExchangeRate::query()
            ->where('base_currency', strtoupper($baseCurrency))
            ->where('quote_currency', strtoupper($quoteCurrency))
            ->whereDate('date', '<=', $date->toDateString())
            ->where('source', 'nbp')
            ->latest('date')
            ->value('rate');
    }

    /**
     * @return array<string, mixed>
     */
    private function record(string $baseCurrency, string $quoteCurrency, string $date, float $rate): array
    {
        return [
            'base_currency' => $baseCurrency,
            'quote_currency' => $quoteCurrency,
            'rate' => $rate,
            'date' => $date,
            'source' => 'nbp',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
