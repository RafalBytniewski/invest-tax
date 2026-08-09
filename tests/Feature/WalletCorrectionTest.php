<?php

namespace Tests\Feature;

use App\Livewire\MyWallet\Show;
use App\Models\Broker;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WalletCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_correction_reconciles_the_wallet_to_the_actual_cash_balance(): void
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
            'name' => 'Main wallet',
            'currency' => 'PLN',
        ]);
        WalletLedger::create([
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 450,
            'date' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['wallet' => $wallet])
            ->call('toggleFundsForm')
            ->set('fundType', 'correction')
            ->assertSee('Actual cash balance')
            ->set('fundAmount', 500)
            ->set('fundDate', now()->format('Y-m-d'))
            ->set('fundNotes', 'Saldo zweryfikowane u brokera')
            ->call('saveFunds')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('wallet_ledgers', [
            'wallet_id' => $wallet->id,
            'type' => 'correction',
            'amount' => 50,
            'notes' => 'Saldo zweryfikowane u brokera',
        ]);
        $this->assertSame(500.0, $wallet->fresh()->cashBalance());
    }

    public function test_correction_requires_notes(): void
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
            'name' => 'Main wallet',
            'currency' => 'PLN',
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['wallet' => $wallet])
            ->call('toggleFundsForm')
            ->set('fundType', 'correction')
            ->set('fundAmount', 50)
            ->set('fundDate', now()->format('Y-m-d'))
            ->set('fundNotes', null)
            ->call('saveFunds')
            ->assertHasErrors(['fundNotes' => 'required']);
    }
}
