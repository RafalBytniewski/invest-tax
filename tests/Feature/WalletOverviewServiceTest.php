<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\Broker;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Wallet\WalletOverviewService;
use Tests\TestCase;

class WalletOverviewServiceTest extends TestCase
{
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
        $wallet = Wallet::create([
            'user_id' => $user->id,
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
}
