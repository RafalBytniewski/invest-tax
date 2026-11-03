<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\Broker;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Transaction\TransactionService;
use App\Services\Wallet\WalletSnapshotService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class WalletSnapshotTest extends TestCase
{
    public function test_it_captures_a_daily_portfolio_value_snapshot(): void
    {
        $wallet = $this->createWallet();
        $asset = Asset::create([
            'symbol' => 'ABC',
            'name' => 'Test asset',
            'asset_type' => 'stock',
        ]);

        AssetPrice::create([
            'asset_id' => $asset->id,
            'close_price' => 120,
            'source' => 'test',
            'date' => '2026-10-14',
        ]);

        app(TransactionService::class)->create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 1,
            'price_per_unit' => 100,
            'exchange_rate' => 1,
            'total_value' => 100,
            'date' => '2026-10-14',
        ]);

        app(WalletSnapshotService::class)->captureDaily(
            $wallet,
            CarbonImmutable::parse('2026-10-14'),
        );

        $this->assertDatabaseHas('wallet_snapshots', [
            'wallet_id' => $wallet->id,
            'snapshot_type' => 'daily',
            'cash_value' => -100,
            'positions_value' => 120,
            'total_value' => 20,
            'valuation_status' => 'complete',
        ]);
    }

    public function test_wallet_index_only_loads_wallets_owned_by_the_authenticated_user(): void
    {
        $wallet = $this->createWallet();
        $otherWallet = $this->createWallet();

        $this->actingAs($wallet->user);

        $this->get(route('my-wallets'))
            ->assertOk()
            ->assertSee($wallet->name)
            ->assertDontSee($otherWallet->name);
    }

    private function createWallet(): Wallet
    {
        $user = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.test',
        ]);

        return Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => 'Wallet '.$user->id,
            'currency' => 'PLN',
        ]);
    }
}
