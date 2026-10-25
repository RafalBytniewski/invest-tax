<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Broker;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Transaction\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionLedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_ledger_entry_for_a_buy_transaction(): void
    {
        $wallet = $this->createWallet();
        $asset = $this->createAsset();

        $transaction = app(TransactionService::class)->create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 2,
            'price_per_unit' => 125.50,
            'total_value' => 251,
            'date' => '2026-10-14',
        ]);

        $this->assertDatabaseHas('wallet_ledgers', [
            'transaction_id' => $transaction->id,
            'wallet_id' => $wallet->id,
            'type' => 'buy',
            'amount' => -251,
            'date' => '2026-10-14 00:00:00',
        ]);
    }

    public function test_it_updates_the_existing_ledger_entry_when_a_transaction_changes(): void
    {
        $wallet = $this->createWallet();
        $asset = $this->createAsset();
        $service = app(TransactionService::class);

        $transaction = $service->create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 2,
            'price_per_unit' => 100,
            'total_value' => 200,
            'date' => '2026-10-14',
        ]);

        $service->update($transaction, [
            'type' => 'sell',
            'total_value' => -150,
            'date' => '2026-10-15',
        ]);

        $this->assertDatabaseCount('wallet_ledgers', 1);
        $this->assertDatabaseHas('wallet_ledgers', [
            'transaction_id' => $transaction->id,
            'type' => 'sell',
            'amount' => 150,
            'date' => '2026-10-15 00:00:00',
        ]);
    }

    public function test_it_deletes_the_ledger_entry_when_a_transaction_is_deleted(): void
    {
        $wallet = $this->createWallet();
        $asset = $this->createAsset();
        $service = app(TransactionService::class);

        $transaction = $service->create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'sell',
            'currency' => 'PLN',
            'quantity' => -1,
            'price_per_unit' => 100,
            'total_value' => -100,
            'date' => '2026-10-14',
        ]);

        $service->delete($transaction);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertDatabaseMissing('wallet_ledgers', ['transaction_id' => $transaction->id]);
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
            'name' => 'Main wallet',
            'currency' => 'PLN',
        ]);
    }

    private function createAsset(): Asset
    {
        return Asset::create([
            'symbol' => 'ABC',
            'name' => 'Test asset',
            'asset_type' => 'stock',
        ]);
    }
}
