<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\Broker;
use App\Models\Exchange;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Wallet\WalletOverviewService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WalletOverviewServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_overview_uses_fifo_prices_from_database_and_only_returns_user_wallets(): void
    {
        $user = User::factory()->create();
        $foreignUser = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test Broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.test',
        ]);
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => 'Main wallet',
            'currency' => 'PLN',
        ]);
        Wallet::create([
            'user_id' => $foreignUser->id,
            'broker_id' => $broker->id,
            'name' => 'Foreign wallet',
            'currency' => 'PLN',
        ]);
        $asset = Asset::create([
            'symbol' => 'ABC',
            'name' => 'Test asset',
            'asset_type' => 'stock',
        ]);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 10,
            'price_per_unit' => 10,
            'total_value' => 100,
            'date' => '2026-01-01',
        ]);
        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 10,
            'price_per_unit' => 20,
            'total_value' => 200,
            'date' => '2026-01-02',
        ]);
        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'sell',
            'currency' => 'PLN',
            'quantity' => -5,
            'price_per_unit' => 20,
            'total_value' => -100,
            'date' => '2026-01-03',
        ]);
        AssetPrice::create([
            'asset_id' => $asset->id,
            'close_price' => 25,
            'source' => 'test',
            'date' => '2026-01-04',
        ]);

        $overview = app(WalletOverviewService::class)->forUser($user);

        $this->assertCount(1, $overview);
        $this->assertSame($wallet->id, $overview[0]['wallet']->id);
        $this->assertSame(1, $overview[0]['overview']['active_assets_count']);
        $this->assertSame(375.0, $overview[0]['overview']['portfolio_value']);
        $this->assertSame(250.0, $overview[0]['overview']['cost_basis']);
        $this->assertSame(125.0, $overview[0]['overview']['unrealized_pl']);
        $this->assertSame(50.0, $overview[0]['overview']['realized_pl']);
        $this->assertSame('04.01.2026', $overview[0]['overview']['last_price_date']);
        $this->assertSame(100.0, $overview[0]['overview']['allocation'][0]['percentage']);
    }

    public function test_missing_close_price_does_not_break_overview_calculations(): void
    {
        $user = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test Broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.test',
        ]);
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => 'Wallet without price',
            'currency' => 'PLN',
        ]);
        $asset = Asset::create([
            'symbol' => 'NOP',
            'name' => 'No price asset',
            'asset_type' => 'stock',
        ]);
        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 1,
            'price_per_unit' => 100,
            'total_value' => 100,
            'date' => '2026-01-01',
        ]);

        $overview = app(WalletOverviewService::class)->forWallet($wallet);

        $this->assertNull($overview['portfolio_value']);
        $this->assertNull($overview['unrealized_pl']);
        $this->assertSame([], $overview['allocation']);
    }

    public function test_it_fetches_one_exchange_rate_for_multiple_assets_in_the_same_currency(): void
    {
        Http::preventStrayRequests();
        $this->travelTo('2026-10-23 12:00:00');
        Http::fake([
            'https://api.nbp.pl/api/exchangerates/rates/a/USD/2026-10-23/?format=json' => Http::response([
                'rates' => [['mid' => 4.0]],
            ]),
        ]);

        $user = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test Broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.test',
        ]);
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => 'USD assets wallet',
            'currency' => 'PLN',
        ]);
        $exchange = Exchange::create([
            'name' => 'NYSE',
            'symbol' => 'NYSE',
            'country' => 'United States',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
            'url' => 'https://example.test',
            'trading_hours' => '09:30-16:00',
        ]);
        $firstAsset = Asset::create([
            'symbol' => 'AAA',
            'name' => 'First asset',
            'asset_type' => 'stock',
            'exchange_id' => $exchange->id,
        ]);
        $secondAsset = Asset::create([
            'symbol' => 'BBB',
            'name' => 'Second asset',
            'asset_type' => 'stock',
            'exchange_id' => $exchange->id,
        ]);

        foreach ([[$firstAsset, 1], [$secondAsset, 2]] as [$asset, $quantity]) {
            Transaction::create([
                'wallet_id' => $wallet->id,
                'asset_id' => $asset->id,
                'type' => 'buy',
                'currency' => 'PLN',
                'quantity' => $quantity,
                'price_per_unit' => 10,
                'total_value' => $quantity * 10,
                'date' => '2026-01-01',
            ]);
            AssetPrice::create([
                'asset_id' => $asset->id,
                'close_price' => 10,
                'source' => 'test',
                'date' => '2026-01-02',
            ]);
        }

        $overview = app(WalletOverviewService::class)->forWallet($wallet);

        Http::assertSentCount(1);
        $this->assertSame(120.0, $overview['portfolio_value']);
    }
}
