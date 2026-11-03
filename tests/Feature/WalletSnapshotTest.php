<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetPrice;
use App\Models\Broker;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletSnapshot;
use App\Services\Transaction\TransactionService;
use App\Services\Wallet\WalletSnapshotService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_captures_a_daily_portfolio_value_snapshot(): void
    {
        $wallet = $this->createWallet();
        $wallet->forceFill(['created_at' => '2026-10-01 00:00:00'])->save();
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

    public function test_it_does_not_capture_a_snapshot_before_wallet_existed_or_before_its_first_transaction(): void
    {
        $wallet = $this->createWallet();
        $asset = Asset::create([
            'symbol' => 'ABC',
            'name' => 'Test asset',
            'asset_type' => 'stock',
        ]);
        $wallet->forceFill(['created_at' => '2026-10-10 00:00:00'])->save();

        $service = app(WalletSnapshotService::class);

        $this->assertNull($service->captureDaily($wallet, CarbonImmutable::parse('2026-10-09')));

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

        $this->assertNull($service->captureDaily($wallet, CarbonImmutable::parse('2026-10-13')));
        $this->assertDatabaseCount('wallet_snapshots', 0);
    }

    public function test_update_fills_only_missing_daily_snapshots_and_reset_deletes_them(): void
    {
        $wallet = $this->createWallet();
        $wallet->forceFill(['created_at' => '2026-10-10 00:00:00'])->save();
        $asset = Asset::create([
            'symbol' => 'ABC',
            'name' => 'Test asset',
            'asset_type' => 'stock',
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

        WalletSnapshot::query()
            ->where('wallet_id', $wallet->id)
            ->update(['total_value' => 999]);

        $this->artisan('wallet-snapshots:capture --update')->assertSuccessful();

        $expectedSnapshotCount = CarbonImmutable::parse('2026-10-14')->diffInDays(CarbonImmutable::today()) + 1;

        $this->assertDatabaseCount('wallet_snapshots', $expectedSnapshotCount);
        $this->assertDatabaseHas('wallet_snapshots', [
            'wallet_id' => $wallet->id,
            'snapshot_at' => '2026-10-14 23:59:59',
            'total_value' => 999,
        ]);

        $this->artisan('wallet-snapshots:capture --reset')->assertSuccessful();

        $this->assertDatabaseCount('wallet_snapshots', 0);
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
