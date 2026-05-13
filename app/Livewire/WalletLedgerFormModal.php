<?php

namespace App\Livewire;

use App\Models\Wallet;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class WalletLedgerFormModal extends Component
{
    public bool $showModal = false;
    public ?int $wallet_id = null;
    public string $source = 'manual_deposit';
    public $amount = null;
    public ?string $date = null;
    public ?string $notes = null;
    public ?string $walletName = null;
    public ?string $walletCurrency = null;

    protected $listeners = [
        'openWalletLedgerFormModal' => 'openModal',
    ];

    public function openModal(int $walletId, string $source = 'manual_deposit'): void
    {
        $wallet = Wallet::query()
            ->where('user_id', Auth::id())
            ->findOrFail($walletId);

        $this->resetValidation();
        $this->wallet_id = $wallet->id;
        $this->walletName = $wallet->name;
        $this->walletCurrency = $wallet->currency;
        $this->source = in_array($source, ['manual_deposit', 'manual_withdrawal'], true)
            ? $source
            : 'manual_deposit';
        $this->amount = null;
        $this->date = now()->format('Y-m-d');
        $this->notes = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'source' => 'required|in:manual_deposit,manual_withdrawal',
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        WalletLedger::create([
            'wallet_id' => (int) $validated['wallet_id'],
            'transaction_id' => null,
            'type' => $validated['source'] === 'manual_deposit' ? 'inflow' : 'outflow',
            'source' => $validated['source'],
            'amount' => (float) $validated['amount'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        session()->flash('success', $validated['source'] === 'manual_deposit'
            ? 'Funds added to wallet.'
            : 'Funds withdrawn from wallet.');

        $this->closeModal();
        $this->dispatch('walletLedgerSaved');
    }

    public function render()
    {
        return view('livewire.wallet-ledger-form-modal');
    }
}
