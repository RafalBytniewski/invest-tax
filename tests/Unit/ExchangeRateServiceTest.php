<?php

namespace Tests\Unit;

use App\Services\Currency\ExchangeRateService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    public function test_it_uses_the_last_available_rate_when_today_has_no_nbp_rate(): void
    {
        Http::preventStrayRequests();
        $this->travelTo('2026-10-25 12:00:00');
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/a/USD/2026-10-25/?format=json' => Http::response([], 404),
            'https://api.nbp.pl/api/exchangerates/rates/a/USD/last/1/?format=json' => Http::response([
                'rates' => [['mid' => 4.25]],
            ]),
        ]);

        $rate = app(ExchangeRateService::class)->getCurrencyPrice('USD', '2026-10-25');

        $this->assertSame(4.25, $rate);
        Http::assertSentCount(2);
    }
}
