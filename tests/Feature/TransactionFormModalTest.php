<?php

namespace Tests\Feature;

use App\Livewire\Transaction\FormModal;
use App\Models\Asset;
use App\Models\Broker;
use App\Models\Exchange;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionFormModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_transaction_can_be_opened_with_wallet_and_asset_context(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $asset = $this->createAsset('Apple');

        $this->actingAs($user);

        Livewire::test(FormModal::class)
            ->call('openCreateModal', $wallet->id, $asset->id)
            ->assertSet('showModal', true)
            ->assertSet('wallet', $wallet->id)
            ->assertSet('asset', $asset->id)
            ->assertSet('walletCurrency', 'PLN')
            ->assertSet('priceCurrency', 'PLN')
            ->assertSet('exchange_rate', 1);
    }

    public function test_user_cannot_open_a_foreign_wallet_in_the_transaction_form(): void
    {
        $user = User::factory()->create();
        $foreignUser = User::factory()->create();
        $foreignWallet = $this->createWallet($foreignUser, 'Foreign wallet');

        $this->actingAs($user);

        Livewire::test(FormModal::class)
            ->call('openCreateModal', $foreignWallet->id)
            ->assertSet('showModal', false)
            ->assertSet('wallet', null);
    }

    public function test_transaction_is_saved_with_a_signed_total_value(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $asset = $this->createAsset('Apple');

        $this->actingAs($user);

        Livewire::test(FormModal::class)
            ->set('wallet', $wallet->id)
            ->set('asset', $asset->id)
            ->set('type', 'sell')
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
            'exchange_rate' => 1,
            'total_value' => -251,
        ]);

        $transaction = $wallet->transactions()->firstOrFail();

        $this->assertDatabaseHas('wallet_ledgers', [
            'transaction_id' => $transaction->id,
            'type' => 'sell',
            'amount' => 251,
        ]);
    }

    public function test_transaction_converts_price_currency_to_wallet_currency_and_saves_multiple_fees(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $exchange = $this->createExchange('USD');
        $asset = $this->createAsset('Apple', $exchange);

        $this->actingAs($user);
        Storage::fake('local');

        $proof = UploadedFile::fake()->create('purchase-proof.pdf', 100, 'application/pdf');

        Livewire::test(FormModal::class)
            ->set('wallet', $wallet->id)
            ->set('asset', $asset->id)
            ->set('type', 'buy')
            ->set('quantity', 2)
            ->set('price_per_unit', 125.50)
            ->set('exchange_rate', 4)
            ->set('fees', [
                ['type' => 'exchange/broker', 'currency' => 'GBP', 'value' => 2.50],
                ['type' => 'network', 'currency' => 'USD', 'value' => 1.25],
            ])
            ->set('attachmentType', 'purchase_proof')
            ->set('attachmentFiles', [$proof])
            ->set('date', '2026-10-14')
            ->call('save');

        $transaction = $wallet->transactions()->firstOrFail();

        $this->assertSame(1004.0, (float) $transaction->total_value);
        $this->assertSame('USD', $transaction->currency);
        $this->assertSame(4.0, (float) $transaction->exchange_rate);
        $this->assertDatabaseCount('fees', 2);
        $this->assertDatabaseHas('fees', [
            'transaction_id' => $transaction->id,
            'type' => 'exchange/broker',
            'currency_type' => 'fiat',
            'currency' => 'GBP',
            'value' => 2.50,
        ]);
        $this->assertDatabaseHas('fees', [
            'transaction_id' => $transaction->id,
            'type' => 'network',
            'currency' => 'USD',
            'value' => 1.25,
        ]);
        $this->assertDatabaseHas('attachments', [
            'transaction_id' => $transaction->id,
            'type' => 'purchase_proof',
            'original_name' => 'purchase-proof.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $attachment = $transaction->attachments()->firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);

        $transaction->delete();

        Storage::disk('local')->assertMissing($attachment->path);
    }

    public function test_editing_total_value_calculates_exchange_rate_and_editing_rate_calculates_total_value(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $asset = $this->createAsset('Apple', $this->createExchange('USD'));

        $this->actingAs($user);

        Livewire::test(FormModal::class)
            ->set('wallet', $wallet->id)
            ->set('asset', $asset->id)
            ->set('type', 'buy')
            ->set('quantity', 2)
            ->set('price_per_unit', 125.50)
            ->set('exchange_rate', 4)
            ->set('total_value', 1004)
            ->assertSet('exchange_rate', 4)
            ->set('exchange_rate', 5)
            ->assertSet('total_value', 1255);
    }

    public function test_transaction_requires_exchange_rate_when_price_and_wallet_currencies_differ(): void
    {
        $user = User::factory()->create();
        $wallet = $this->createWallet($user, 'Main wallet');
        $asset = $this->createAsset('Apple', $this->createExchange('USD'));

        $this->actingAs($user);

        Livewire::test(FormModal::class)
            ->set('wallet', $wallet->id)
            ->set('asset', $asset->id)
            ->set('type', 'buy')
            ->set('quantity', 2)
            ->set('price_per_unit', 125.50)
            ->set('date', '2026-10-14')
            ->call('save')
            ->assertHasErrors(['exchange_rate']);

        $this->assertDatabaseCount('transactions', 0);
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

    private function createExchange(string $currency): Exchange
    {
        return Exchange::create([
            'name' => $currency.' Exchange',
            'symbol' => $currency.'X',
            'country' => 'Testland',
            'currency' => $currency,
            'timezone' => 'UTC',
            'url' => 'https://example.test',
            'trading_hours' => '09:00-17:00',
        ]);
    }

    private function createAsset(string $name, ?Exchange $exchange = null): Asset
    {
        return Asset::create([
            'symbol' => strtoupper(substr($name, 0, 3)),
            'name' => $name,
            'asset_type' => 'stock',
            'exchange_id' => $exchange?->id,
        ]);
    }
}
