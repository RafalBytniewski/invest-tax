<?php

namespace App\Livewire\MyWallet;

use App\Models\Wallet;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    public Wallet $wallet;

    public bool $showFundsForm = false;

    public string $fundType = 'deposit';

    public $fundAmount = null;

    public ?string $fundDate = null;

    public ?string $fundNotes = null;

    public function mount(Wallet $wallet): void
    {
        abort_unless($wallet->user_id === Auth::id(), 403);

        $this->wallet = $wallet;
        $this->fundDate = now()->format('Y-m-d');
        $this->refreshWallet();
    }

    public function toggleFundsForm(): void
    {
        $this->showFundsForm = ! $this->showFundsForm;
    }

    public function saveFunds(): void
    {
        $validated = $this->validate([
            'fundType' => ['required', Rule::in(['deposit', 'withdraw'])],
            'fundAmount' => ['required', 'numeric', 'gt:0'],
            'fundDate' => ['required', 'date'],
            'fundNotes' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = abs((float) $validated['fundAmount']);

        WalletLedger::create([
            'wallet_id' => $this->wallet->id,
            'type' => $validated['fundType'],
            'amount' => $validated['fundType'] === 'withdraw' ? -$amount : $amount,
            'date' => $validated['fundDate'],
            'notes' => $validated['fundNotes'] ?? null,
        ]);

        $this->resetFundsForm();
        $this->showFundsForm = false;
        $this->refreshWallet();
        session()->flash('success', 'Wallet funds updated.');
    }

    protected function resetFundsForm(): void
    {
        $this->fundType = 'deposit';
        $this->fundAmount = null;
        $this->fundDate = now()->format('Y-m-d');
        $this->fundNotes = null;
        $this->resetValidation();
    }

    protected function refreshWallet(): void
    {
        $this->wallet->load([
            'broker',
            'walletLedgers' => fn ($query) => $query
                ->with('transaction.asset')
                ->latest('date')
                ->latest('id'),
            'transactions' => fn ($query) => $query
                ->with(['asset.exchange'])
                ->latest('date'),
        ]);

        $this->wallet->setAttribute('active_assets_count', $this->wallet->activeAssetsCollection()->count());
        $this->wallet->setAttribute('transactions_count', $this->wallet->transactions->count());
        $this->wallet->setAttribute('invested_total', $this->wallet->transactions->where('type', 'buy')->sum('total_value'));
        $this->wallet->setAttribute('last_transaction_date', $this->wallet->transactions->first()?->date?->format('d.m.Y'));
        $this->wallet->setAttribute('cash_balance', $this->wallet->cashBalance());
    }

    public function render()
    {
        return view('livewire.my-wallet.show', [
            'transactions' => $this->wallet->transactions->take(10),
            'walletLedgers' => $this->wallet->walletLedgers->take(15),
        ]);
    }
}
