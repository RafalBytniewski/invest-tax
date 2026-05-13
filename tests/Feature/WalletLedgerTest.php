<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Broker;
use App\Models\Exchange;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_inflow_ledger_for_sell_transaction(): void
    {
        [$wallet, $asset] = $this->walletAndAsset();

        $transaction = Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'sell',
            'currency' => 'PLN',
            'quantity' => -2,
            'price_per_unit' => 125,
            'total_value' => -250,
            'date' => now(),
            'affects_wallet_balance' => true,
        ]);

        $this->assertDatabaseHas('wallet_ledgers', [
            'transaction_id' => $transaction->id,
            'wallet_id' => $wallet->id,
            'type' => 'inflow',
            'source' => 'sell',
            'amount' => '250.00000000',
        ]);
    }

    public function test_it_skips_ledger_for_buy_when_wallet_funding_is_disabled(): void
    {
        [$wallet, $asset] = $this->walletAndAsset();

        $transaction = Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 1,
            'price_per_unit' => 100,
            'total_value' => 100,
            'date' => now(),
            'affects_wallet_balance' => false,
        ]);

        $this->assertDatabaseMissing('wallet_ledgers', [
            'transaction_id' => $transaction->id,
        ]);
    }

    public function test_it_recalculates_wallet_cash_balance_from_manual_and_transaction_entries(): void
    {
        [$wallet, $asset] = $this->walletAndAsset();

        WalletLedger::create([
            'wallet_id' => $wallet->id,
            'transaction_id' => null,
            'type' => 'inflow',
            'source' => 'manual_deposit',
            'amount' => 1000,
            'date' => now(),
        ]);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 2,
            'price_per_unit' => 100,
            'total_value' => 200,
            'date' => now(),
            'affects_wallet_balance' => true,
        ]);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => null,
            'type' => 'tax',
            'currency' => 'PLN',
            'quantity' => 1,
            'price_per_unit' => 19,
            'total_value' => 19,
            'date' => now(),
            'affects_wallet_balance' => true,
        ]);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'sell',
            'currency' => 'PLN',
            'quantity' => -1,
            'price_per_unit' => 300,
            'total_value' => -300,
            'date' => now(),
            'affects_wallet_balance' => true,
        ]);

        $wallet->load('ledgers');

        $this->assertSame(1081.0, $wallet->cashBalance());
    }

    protected function walletAndAsset(): array
    {
        $user = User::factory()->create();
        $broker = Broker::create([
            'name' => 'Test Broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://broker.test',
            'country' => 'PL',
        ]);
        $exchange = Exchange::create([
            'name' => 'Warsaw Stock Exchange',
            'symbol' => 'GPW',
            'country' => 'PL',
            'region' => 'EU',
            'currency' => 'PLN',
            'timezone' => 'Europe/Warsaw',
            'url' => 'https://exchange.test',
            'trading_hours' => '09:00-17:00',
        ]);
        $asset = Asset::create([
            'symbol' => 'ABC',
            'name' => 'ABC Corp',
            'asset_type' => 'stock',
            'exchange_id' => $exchange->id,
        ]);
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => 'Main Wallet',
            'currency' => 'PLN',
        ]);

        return [$wallet, $asset];
    }
}
