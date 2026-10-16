<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Fee;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public array $fees = [];

    public array $wallets = [];

    public array $assets = [];

    public array $types = [
        'buy' => 'Buy',
        'sell' => 'Sell',
    ];

    public array $currencies = [
        'PLN' => 'PLN',
        'USD' => 'USD',
        'EUR' => 'EUR',
        'GBP' => 'GBP',
    ];

    public array $feeTypes = [
        'exchange/broker' => 'Exchange/Broker fee',
        'network' => 'Network fee',
        'withdrawal' => 'Withdrawal fee',
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

    public function openCreateModal(?int $walletId = null, ?int $assetId = null): void
    {
        $this->mode = 'new';
        $this->resetForm();

        if ($walletId !== null) {
            $wallet = Wallet::query()->findOrFail($walletId);
            abort_unless($wallet->user_id === Auth::id(), 403);
            $this->wallet = $wallet->id;
            $this->currency = strtoupper((string) $wallet->currency);
        }

        if ($assetId !== null) {
            $this->asset = Asset::query()->findOrFail($assetId)->id;
        }

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
        $this->total_value = abs((float) $transaction->total_value);
        $this->fees = $transaction->fees
            ->map(fn (Fee $fee): array => [
                'type' => $fee->type,
                'currency' => strtoupper((string) $fee->currency),
                'value' => (float) $fee->value,
            ])
            ->values()
            ->all();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function addFee(): void
    {
        $this->fees[] = [
            'type' => 'exchange/broker',
            'currency' => $this->currency ?? 'PLN',
            'value' => null,
        ];
    }

    public function removeFee(int $index): void
    {
        unset($this->fees[$index]);
        $this->fees = array_values($this->fees);
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->currency = strtoupper((string) $this->currency);

        foreach ($this->fees as $index => $fee) {
            $this->fees[$index]['currency'] = strtoupper((string) ($fee['currency'] ?? ''));
        }

        $validated = $this->validate([
            'wallet' => [
                'required',
                Rule::exists('wallets', 'id')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
            'asset' => 'required|exists:assets,id',
            'type' => 'required|in:buy,sell',
            'currency' => ['required', 'string', 'size:3', Rule::in(array_keys($this->currencies))],
            'quantity' => 'required|numeric|gt:0',
            'price_per_unit' => 'required|numeric|gte:0',
            'total_value' => 'required|numeric|gte:0',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'fees' => 'array',
            'fees.*.type' => ['required', Rule::in(array_keys($this->feeTypes))],
            'fees.*.currency' => ['required', 'string', 'size:3', Rule::in(array_keys($this->currencies))],
            'fees.*.value' => 'required|numeric|gt:0',
        ]);

        if ($validated['type'] === 'sell') {
            $validated['quantity'] = -abs((float) $validated['quantity']);
            $validated['total_value'] = -abs((float) $validated['total_value']);
        } else {
            $validated['quantity'] = abs((float) $validated['quantity']);
            $validated['total_value'] = abs((float) $validated['total_value']);
        }

        $payload = [
            'wallet_id' => (int) $validated['wallet'],
            'asset_id' => (int) $validated['asset'],
            'type' => $validated['type'],
            'currency' => strtoupper($validated['currency']),
            'quantity' => $validated['quantity'],
            'price_per_unit' => (float) $validated['price_per_unit'],
            'total_value' => $validated['total_value'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ];

        DB::transaction(function () use ($payload, $validated): void {
            if ($this->editingTransactionId !== null) {
                $transaction = $this->ownedTransactionsQuery()->findOrFail($this->editingTransactionId);
                $transaction->update($payload);
                $transaction->fees()->delete();
            } else {
                $transaction = Transaction::create($payload);
            }

            $transaction->fees()->createMany(array_map(
                fn (array $fee): array => [
                    'type' => $fee['type'],
                    'currency_type' => 'fiat',
                    'currency' => strtoupper($fee['currency']),
                    'value' => (float) $fee['value'],
                ],
                $validated['fees'] ?? [],
            ));
        });

        session()->flash('success', $this->editingTransactionId !== null ? 'Transaction updated.' : 'Transaction created.');

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
        $this->fees = [];
        $this->resetValidation();
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
