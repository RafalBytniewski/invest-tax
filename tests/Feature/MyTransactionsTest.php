<?php

namespace Tests\Feature;

use App\Livewire\MyTransactions;
use App\Models\Asset;
use App\Models\Broker;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MyTransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_can_be_viewed_for_a_wallet_or_asset_context(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $broker = $this->createBroker('Test Broker');
        $otherBroker = $this->createBroker('Other Broker');
        $wallet = $this->createWallet($user, $broker, 'Main wallet');
        $otherWallet = $this->createWallet($user, $otherBroker, 'Other wallet');
        $foreignWallet = $this->createWallet($otherUser, $broker, 'Foreign wallet');
        $asset = $this->createAsset('Apple');
        $otherAsset = $this->createAsset('Bitcoin');

        $walletTransaction = $this->createTransaction($wallet, $asset, '2026-01-15');
        $otherWalletTransaction = $this->createTransaction($otherWallet, $otherAsset, '2026-02-15');
        $foreignTransaction = $this->createTransaction($foreignWallet, $asset, '2026-03-15');

        $this->assertDatabaseHas('transactions', ['id' => $walletTransaction->id]);
        $this->actingAs($user);

        Livewire::test(MyTransactions::class, ['wallet' => $wallet])
            ->assertSet('wallet.id', $wallet->id)
            ->assertSee('2026-01-15')
            ->assertDontSee('2026-02-15')
            ->assertDontSee('2026-03-15');

        Livewire::test(MyTransactions::class, ['asset' => $asset])
            ->assertSee('2026-01-15')
            ->assertDontSee('2026-02-15')
            ->assertDontSee('2026-03-15');

        $this->get(route('my-wallet.transactions', $wallet))
            ->assertOk()
            ->assertSee('Transactions for Main wallet');

        $this->get(route('assets.transactions', $asset))
            ->assertOk()
            ->assertSee('Transactions for Apple');
    }

    public function test_user_cannot_open_another_users_wallet_transactions(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $broker = $this->createBroker('Test Broker');
        $foreignWallet = $this->createWallet($otherUser, $broker, 'Foreign wallet');

        $response = $this->actingAs($user)
            ->get(route('my-wallet.transactions', $foreignWallet));

        $response->assertForbidden();
    }

    private function createBroker(string $name): Broker
    {
        return Broker::create([
            'name' => $name,
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.test',
        ]);
    }

    private function createWallet(User $user, Broker $broker, string $name): Wallet
    {
        return Wallet::create([
            'user_id' => $user->id,
            'broker_id' => $broker->id,
            'name' => $name,
            'currency' => 'PLN',
        ]);
    }

    private function createAsset(string $name): Asset
    {
        return Asset::create([
            'symbol' => strtoupper(substr($name, 0, 3)),
            'name' => $name,
            'asset_type' => 'stock',
        ]);
    }

    private function createTransaction(Wallet $wallet, Asset $asset, string $date): Transaction
    {
        return Transaction::create([
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'buy',
            'currency' => 'PLN',
            'quantity' => 1,
            'price_per_unit' => 100,
            'total_value' => 100,
            'date' => $date,
        ]);
    }
}
