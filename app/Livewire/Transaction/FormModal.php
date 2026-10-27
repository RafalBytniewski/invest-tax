<?php

namespace App\Livewire\Transaction;

use App\Models\Asset;
use App\Models\Fee;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class FormModal extends Component
{
    use WithFileUploads;

    public ?int $editingTransactionId = null;

    public bool $showModal = false;

    public string $mode = '';

    public $wallet = null;

    public $asset = null;

    public $type = null;

    public ?string $walletCurrency = null;

    public ?string $priceCurrency = null;

    public $quantity = null;

    public $price_per_unit = null;

    public $total_value = 0;

    public $exchange_rate = null;

    public $date = null;

    public $notes = null;

    public array $fees = [];

    public array $attachmentFiles = [];

    public string $attachmentType = 'other';

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

    public array $attachmentTypes = [
        'purchase_proof' => 'Purchase proof',
        'fee_proof' => 'Fee proof',
        'statement' => 'Account statement',
        'other' => 'Other',
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

    public function updated(string $field): void
    {
        if ($field === 'total_value') {
            $this->recalculateExchangeRate();

            return;
        }

        if (in_array($field, ['quantity', 'price_per_unit', 'exchange_rate'], true)) {
            $this->recalculateTotalValue();
        }
    }

    public function updatedWallet(): void
    {
        $this->syncCurrencyContext(true);
    }

    public function updatedAsset(): void
    {
        $this->syncCurrencyContext(true);
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
            abort_unless((int) $wallet->user_id === (int) Auth::id(), 403);
            $this->wallet = $wallet->id;
        }

        if ($assetId !== null) {
            $this->asset = Asset::query()->findOrFail($assetId)->id;
        }

        $this->syncCurrencyContext(true);
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
        $this->walletCurrency = strtoupper((string) $transaction->wallet->currency);
        $this->priceCurrency = strtoupper((string) ($transaction->currency ?: $this->resolvePriceCurrency($transaction->asset, $this->walletCurrency)));
        $this->quantity = abs((float) $transaction->quantity);
        $this->price_per_unit = (float) $transaction->price_per_unit;
        $this->date = optional($transaction->date)->format('Y-m-d');
        $this->notes = $transaction->notes;
        $this->total_value = abs((float) $transaction->total_value);
        $this->exchange_rate = $transaction->exchange_rate !== null
            ? (float) $transaction->exchange_rate
            : $this->legacyExchangeRate();
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
            'currency' => $this->walletCurrency ?? 'PLN',
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
        $this->attachmentFiles = [];
        $this->attachmentType = 'other';
        $this->resetValidation();
    }

    public function save(): void
    {
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
            'quantity' => 'required|numeric|gt:0',
            'price_per_unit' => 'required|numeric|gte:0',
            'total_value' => 'required|numeric|gte:0',
            'exchange_rate' => 'nullable|numeric|gt:0',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'fees' => 'array',
            'fees.*.type' => ['required', Rule::in(array_keys($this->feeTypes))],
            'fees.*.currency' => ['required', 'string', 'size:3', Rule::in(array_keys($this->currencies))],
            'fees.*.value' => 'required|numeric|gt:0',
            'attachmentType' => ['required', Rule::in(array_keys($this->attachmentTypes))],
            'attachmentFiles' => 'array|max:2',
            'attachmentFiles.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,csv',
        ]);

        $wallet = Wallet::query()
            ->where('user_id', Auth::id())
            ->findOrFail((int) $validated['wallet']);
        $asset = Asset::query()->with('exchange')->findOrFail((int) $validated['asset']);

        $this->walletCurrency = strtoupper((string) $wallet->currency);
        $this->priceCurrency = $this->resolvePriceCurrency($asset, $this->walletCurrency);

        if ($this->walletCurrency === $this->priceCurrency && $this->exchange_rate === null) {
            $this->exchange_rate = 1;
        } elseif ($this->exchange_rate === null) {
            $this->addError('exchange_rate', 'An exchange rate is required when the currencies differ.');

            return;
        }

        $validated['exchange_rate'] = (float) $this->exchange_rate;

        $calculatedTotalValue = abs(
            (float) $validated['quantity']
            * (float) $validated['price_per_unit']
            * (float) $validated['exchange_rate']
        );

        if ($validated['type'] === 'sell') {
            $validated['quantity'] = -abs((float) $validated['quantity']);
            $validated['total_value'] = -$calculatedTotalValue;
        } else {
            $validated['quantity'] = abs((float) $validated['quantity']);
            $validated['total_value'] = $calculatedTotalValue;
        }

        $this->total_value = abs($validated['total_value']);

        $payload = [
            'wallet_id' => (int) $validated['wallet'],
            'asset_id' => (int) $validated['asset'],
            'type' => $validated['type'],
            'currency' => $this->priceCurrency,
            'quantity' => $validated['quantity'],
            'price_per_unit' => (float) $validated['price_per_unit'],
            'exchange_rate' => (float) $validated['exchange_rate'],
            'total_value' => $validated['total_value'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ];

        $storedAttachmentPaths = [];

        try {
            DB::transaction(function () use ($payload, $validated, &$storedAttachmentPaths): void {
                if ($this->editingTransactionId !== null) {
                    $transaction = $this->ownedTransactionsQuery()->findOrFail($this->editingTransactionId);
                    app(TransactionService::class)->update($transaction, $payload);
                    $transaction->fees()->get()->each->delete();
                } else {
                    $transaction = app(TransactionService::class)->create($payload);
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

                foreach ($this->attachmentFiles as $file) {
                    /** @var UploadedFile $file */
                    $path = $file->store('transaction-attachments', 'local');
                    $storedAttachmentPaths[] = $path;

                    $transaction->attachments()->create([
                        'type' => $validated['attachmentType'],
                        'disk' => 'local',
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                        'size' => $file->getSize(),
                        'hash' => hash_file('sha256', $file->getRealPath()),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedAttachmentPaths);

            throw $exception;
        }

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
        $this->walletCurrency = null;
        $this->priceCurrency = null;
        $this->quantity = null;
        $this->price_per_unit = null;
        $this->total_value = 0;
        $this->exchange_rate = null;
        $this->date = now()->format('Y-m-d');
        $this->notes = null;
        $this->fees = [];
        $this->attachmentFiles = [];
        $this->attachmentType = 'other';
        $this->resetValidation();
    }

    protected function recalculateTotalValue(): void
    {
        $quantity = (float) ($this->quantity ?? 0);
        $price = (float) ($this->price_per_unit ?? 0);
        $exchangeRate = (float) ($this->exchange_rate ?? 0);
        $this->total_value = $quantity * $price * $exchangeRate;
    }

    protected function recalculateExchangeRate(): void
    {
        $marketValue = (float) ($this->quantity ?? 0) * (float) ($this->price_per_unit ?? 0);

        if ($marketValue > 0 && (float) ($this->total_value ?? 0) > 0) {
            $this->exchange_rate = (float) $this->total_value / $marketValue;
        }
    }

    protected function syncCurrencyContext(bool $resetExchangeRate = false): void
    {
        $wallet = $this->wallet
            ? Wallet::query()->where('user_id', Auth::id())->find($this->wallet)
            : null;
        $asset = $this->asset
            ? Asset::query()->with('exchange')->find($this->asset)
            : null;

        $this->walletCurrency = $wallet?->currency
            ? strtoupper((string) $wallet->currency)
            : null;
        $this->priceCurrency = $this->resolvePriceCurrency($asset, $this->walletCurrency);

        if ($resetExchangeRate || $this->walletCurrency === $this->priceCurrency) {
            $this->exchange_rate = $this->walletCurrency !== null
                && $this->walletCurrency === $this->priceCurrency
                ? 1
                : null;
        }

        $this->recalculateTotalValue();
    }

    protected function resolvePriceCurrency(?Asset $asset, ?string $walletCurrency): ?string
    {
        if ($asset === null) {
            return null;
        }

        $currency = $asset->exchange?->currency;

        if ($currency === null && $asset->asset_type === 'crypto') {
            $currency = 'USD';
        }

        return strtoupper((string) ($currency ?: $walletCurrency)) ?: null;
    }

    protected function legacyExchangeRate(): float
    {
        $marketValue = (float) $this->quantity * (float) $this->price_per_unit;

        if ($marketValue <= 0) {
            return $this->walletCurrency === $this->priceCurrency ? 1 : 0;
        }

        return abs((float) $this->total_value) / $marketValue;
    }

    protected function ownedTransactionsQuery()
    {
        return Transaction::query()->whereHas('wallet', function ($query) {
            $query->where('user_id', Auth::id());
        });
    }

    public function render()
    {
        return view('livewire.transaction.form-modal', [
            'wallets' => $this->wallets,
            'assets' => $this->assets,
        ]);
    }
}
