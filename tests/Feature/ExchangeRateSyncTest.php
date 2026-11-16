<?php

namespace Tests\Feature;

use App\Services\Currency\ExchangeRateService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_command_stores_both_exchange_rate_directions(): void
    {
        Http::fake([
            'api.nbp.pl/*' => Http::response([
                'rates' => [
                    ['effectiveDate' => '2026-11-10', 'mid' => 4.25],
                ],
            ]),
        ]);

        $this->artisan('exchange-rates:sync --from=2026-11-10 --to=2026-11-11')
            ->expectsOutputToContain('Exchange rates synchronized')
            ->assertExitCode(0);

        $this->assertDatabaseCount('exchange_rates', 2);
        $this->assertDatabaseHas('exchange_rates', [
            'base_currency' => 'EUR',
            'quote_currency' => 'PLN',
            'rate' => '4.2500000000',
            'date' => '2026-11-10',
            'source' => 'nbp',
        ]);

        $this->assertDatabaseHas('exchange_rates', [
            'base_currency' => 'PLN',
            'quote_currency' => 'EUR',
            'date' => '2026-11-10',
            'source' => 'nbp',
        ]);

        Http::assertSent(fn (Request $request): bool => str_contains(
            $request->url(),
            '/api/exchangerates/rates/a/eur/2026-11-10/2026-11-11/'
        ));
    }

    public function test_repeating_the_sync_does_not_create_duplicates(): void
    {
        Http::fake([
            'api.nbp.pl/*' => Http::response([
                'rates' => [
                    ['effectiveDate' => '2026-11-10', 'mid' => 4.25],
                ],
            ]),
        ]);

        $this->artisan('exchange-rates:sync --from=2026-11-10 --to=2026-11-10')->assertExitCode(0);
        $this->artisan('exchange-rates:sync --from=2026-11-10 --to=2026-11-10')->assertExitCode(0);

        $this->assertDatabaseCount('exchange_rates', 2);
    }

    public function test_it_returns_the_latest_available_rate_before_the_requested_date(): void
    {
        Http::fake([
            'api.nbp.pl/*' => Http::response([
                'rates' => [
                    ['effectiveDate' => '2026-11-06', 'mid' => 4.25],
                ],
            ]),
        ]);

        $this->artisan('exchange-rates:sync --from=2026-11-06 --to=2026-11-06')->assertExitCode(0);

        $rate = app(ExchangeRateService::class)->getRate(
            'EUR',
            'PLN',
            CarbonImmutable::parse('2026-11-08')
        );

        $this->assertEqualsWithDelta(4.25, (float) $rate, 0.00000001);
    }
}
