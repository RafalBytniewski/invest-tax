<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TransactionFormModal extends Component
{
    public ?int $editingTransactionId = null;
    public bool $showModal = false;
    public string $mode = '';

    public $wallet = null;
    public $asset = null;
    public $type = null;
    public $currency = null;
    public $quantity = null;
    public $price_per_unit = null;
    public $total_value = 0;
    public $date = null;
    public $notes = null;
    public bool $affects_wallet_balance = true;
    public $total_fees = 0;
    public $attachments = null;

    public array $wallets = [];
    public array $assets = [];
    public array $types = [
        'buy' => 'Buy',
        'sell' => 'Sell',
        'dividend' => 'Dividend',
        'crypto_reward' => 'Crypto Reward',
        'tax' => 'Tax',
    ];

    protected $listeners = [
        'openTransactionFormModal' => 'openTransactionFormModal',
        'openNewTransactionModal' => 'openCreateModal',
    ];

    public function mount(): void
    {
        $this->wallets = Wallet::where('user_id', Auth::id())
            ->pluck('name', 'id')
            ->toArray();

        $this->assets = Asset::pluck('name', 'id')->toArray();
    }

    public function updated($field): void
    {
        if (in_array($field, ['quantity', 'price_per_unit'], true)) {
            $this->recalculateTotalValue();
        }
    }

    public function openTransactionFormModal(?int $transactionId = null): void
    {
        if ($transactionId === null) {
            $this->openCreateModal();
            return;
        }

        $this->openEditModal($transactionId);
    }

    public function openCreateModal(): void
    {
        $this->mode = 'new';
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $transactionId): void
    {
        $this->mode = 'edit';
        $transaction = $this->ownedTransactionsQuery()->findOrFail($transactionId);

        $this->editingTransactionId = $transaction->id;
        $this->wallet = $transaction->wallet_id;
        $this->asset = $transaction->asset_id;
        $this->type = $transaction->type;
        $this->currency = strtoupper((string) $transaction->currency);
        $this->quantity = abs((float) $transaction->quantity);
        $this->price_per_unit = (float) $transaction->price_per_unit;
        $this->date = optional($transaction->date)->format('Y-m-d');
        $this->notes = $transaction->notes;
        $this->affects_wallet_balance = (bool) $transaction->affects_wallet_balance;
        $this->recalculateTotalValue();
        $this->resetValidation();
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
            'wallet' => [
                'required',
                Rule::exists('wallets', 'id')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'asset' => [
                Rule::requiredIf($this->type !== 'tax'),
                'nullable',
                'exists:assets,id',
            ],
            'type' => 'required|in:buy,sell,dividend,crypto_reward,tax',
            'currency' => 'required|string|size:3',
            'quantity' => 'required|numeric|gt:0',
            'price_per_unit' => 'required|numeric|gte:0',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'affects_wallet_balance' => 'required|boolean',
        ]);

        $baseTotalValue = (float) $validated['quantity'] * (float) $validated['price_per_unit'];

        if ($validated['type'] === 'sell') {
            $validated['quantity'] = -abs((float) $validated['quantity']);
            $validated['total_value'] = -abs($baseTotalValue);
        } else {
            $validated['quantity'] = abs((float) $validated['quantity']);
            $validated['total_value'] = abs($baseTotalValue);
        }

        $affectsWalletBalance = $validated['type'] === 'buy'
            ? (bool) $validated['affects_wallet_balance']
            : true;

        $payload = [
            'wallet_id' => (int) $validated['wallet'],
            'asset_id' => $validated['asset'] !== null ? (int) $validated['asset'] : null,
            'type' => $validated['type'],
            'currency' => strtoupper($validated['currency']),
            'quantity' => $validated['quantity'],
            'price_per_unit' => (float) $validated['price_per_unit'],
            'total_value' => $validated['total_value'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
            'affects_wallet_balance' => $affectsWalletBalance,
        ];

        if ($this->editingTransactionId !== null) {
            $transaction = $this->ownedTransactionsQuery()->findOrFail($this->editingTransactionId);
            $transaction->update($payload);
            session()->flash('success', 'Transaction updated.');
        } else {
            Transaction::create($payload);
            session()->flash('success', 'Transaction created.');
        }

        $this->closeModal();
        $this->dispatch('transactionSaved');
    }

    protected function resetForm(): void
    {
        $this->editingTransactionId = null;
        $this->wallet = null;
        $this->asset = null;
        $this->type = null;
        $this->currency = null;
        $this->quantity = null;
        $this->price_per_unit = null;
        $this->total_value = 0;
        $this->date = now()->format('Y-m-d');
        $this->notes = null;
        $this->affects_wallet_balance = true;
        $this->resetValidation();
    }

    public function updatedType($value): void
    {
        if ($value !== 'buy') {
            $this->affects_wallet_balance = true;
        }

        if ($value === 'tax') {
            $this->asset = null;
        }
    }

    protected function recalculateTotalValue(): void
    {
        $quantity = (float) ($this->quantity ?? 0);
        $price = (float) ($this->price_per_unit ?? 0);
        $this->total_value = $quantity * $price;
    }

    protected function ownedTransactionsQuery()
    {
        return Transaction::query()->whereHas('wallet', function ($query) {
            $query->where('user_id', Auth::id());
        });
    }

    public function render()
    {
        return view('livewire.transaction-form-modal', [
            'wallets' => $this->wallets,
            'assets' => $this->assets,
        ]);
    }
}
