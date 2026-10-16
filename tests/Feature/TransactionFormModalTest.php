<?php

namespace Tests\Feature;

use App\Livewire\TransactionFormModal;
use App\Models\Asset;
use App\Models\Broker;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionFormModalTest extends TestCase
{
    public function test_new_transaction_can_be_opened_with_wallet_and_asset_context(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $asset = $this->createAsset('Apple');

        $this->actingAs($user);

        Livewire::test(TransactionFormModal::class)
            ->call('openCreateModal', $wallet->id, $asset->id)
            ->assertSet('showModal', true)
            ->assertSet('wallet', $wallet->id)
            ->assertSet('asset', $asset->id)
            ->assertSet('currency', 'PLN');
    }

    public function test_user_cannot_open_a_foreign_wallet_in_the_transaction_form(): void
    {
        $user = User::factory()->create();
        $foreignUser = User::factory()->create();
        $foreignWallet = $this->createWallet($foreignUser, 'Foreign wallet');

        $this->actingAs($user);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        Livewire::test(TransactionFormModal::class)
            ->call('openCreateModal', $foreignWallet->id);
    }

    public function test_transaction_is_saved_with_a_signed_total_value(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $asset = $this->createAsset('Apple');

        $this->actingAs($user);

        Livewire::test(TransactionFormModal::class)
            ->set('wallet', $wallet->id)
            ->set('asset', $asset->id)
            ->set('type', 'sell')
            ->set('currency', 'pln')
            ->set('quantity', 2)
            ->set('price_per_unit', 125.50)
            ->set('date', '2026-10-14')
            ->call('save')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('transactions', [
            'wallet_id' => $wallet->id,
            'asset_id' => $asset->id,
            'type' => 'sell',
            'currency' => 'PLN',
            'quantity' => -2,
            'price_per_unit' => 125.50,
            'total_value' => -251,
        ]);
    }

    private function createWallet(User $user, string $name): Wallet
    {
        $broker = Broker::create([
            'name' => $name.' broker',
            'type' => 'broker',
            'image' => 'broker.png',
            'url' => 'https://example.test',
        ]);

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
}
