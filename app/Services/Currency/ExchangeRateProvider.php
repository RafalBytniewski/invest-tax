<?php

namespace App\Services\Currency;

use Carbon\CarbonImmutable;

interface ExchangeRateProvider
{
    /**
     * @return list<array{date: string, rate: float}>
     */
    public function rates(string $currency, CarbonImmutable $from, CarbonImmutable $to): array;
}
