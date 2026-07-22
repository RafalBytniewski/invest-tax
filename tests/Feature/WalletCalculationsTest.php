<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Broker;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletCalculationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_uses_fifo_for_average_cost_and_realized_profit(): void
    {
        $wallet = $this->createWallet();
        $asset = Asset::create([
            'symbol' => 'TEST',
            'name' => 'Test asset',
            'asset_type' => 'stock',
        ]);

        $this->createTransaction($wallet, $asset, 'buy', 10, 1000, '2026-01-01');
        $this->createTransaction($wallet, $asset, 'sell', -4, -600, '2026-01-02');
        $this->createTransaction($wallet, $asset, 'buy', 4, 800, '2026-01-03');

        $summary = $wallet->assetSummary($asset->id);

        $this->assertEqualsWithDelta(10, $summary['quantity'], 0.00000001);
        $this->assertEqualsWithDelta(140, $wallet->averageBuyPrice($asset->id), 0.00000001);
        $this->assertEqualsWithDelta(200, $wallet->realizedPL(), 0.00000001);
    }

    public function test_wallet_calculates_fifo_separately_for_each_asset(): void
    {
        $wallet = $this->createWallet();
        $firstAsset = Asset::create([
            'symbol' => 'ONE',
            'name' => 'First asset',
            'asset_type' => 'stock',
        ]);
        $secondAsset = Asset::create([
            'symbol' => 'TWO',
            'name' => 'Second asset',
            'asset_type' => 'stock',
        ]);

        $this->createTransaction($wallet, $firstAsset, 'buy', 1, 100, '2026-01-01');
        $this->createTransaction($wallet, $secondAsset, 'buy', 1, 1000, '2026-01-01');
        $this->createTransaction($wallet, $firstAsset, 'sell', -1, -150, '2026-01-02');
        $this->createTransaction($wallet, $secondAsset, 'sell', -1, -900, '2026-01-02');

        $this->assertEqualsWithDelta(-50, $wallet->realizedPL(), 0.00000001);
    }

    private function createWallet(): Wallet
    {
        $broker = Broker::create([
            'name' => 'Test broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.com',
        ]);

        return Wallet::create([
            'name' => 'Test wallet',
            'currency' => 'PLN',
            'user_id' => User::factory()->create()->id,
            'broker_id' => $broker->id,
        ]);
    }

    private function createTransaction(
        Wallet $wallet,
        Asset $asset,
        string $type,
        float $quantity,
        float $totalValue,
        string $date,
    ): Transaction {
        return Transaction::create([
            'type' => $type,
            'currency' => 'PLN',
            'quantity' => $quantity,
            'price_per_unit' => abs($totalValue / $quantity),
            'total_value' => $totalValue,
            'date' => $date,
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
        ]);
    }
}
