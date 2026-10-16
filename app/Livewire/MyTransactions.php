<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyTransactions extends Component
{
    use WithPagination;

    protected const ALLOWED_SORT_FIELDS = ['asset', 'broker', 'wallet', 'type', 'quantity', 'price', 'total_value', 'date'];

    protected $listeners = ['transactionSaved' => '$refresh'];

    public string $perPage = '10';

    public string $search = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $sortField = 'date';

    public string $sortDirection = 'desc';

    public ?Wallet $wallet = null;

    public ?Asset $asset = null;

    protected $queryString = [
        'search',
        'dateFrom',
        'dateTo',
        'sortField',
        'sortDirection',
    ];

    public function mount(?Wallet $wallet = null, ?Asset $asset = null): void
    {
        if ($wallet?->exists) {
            abort_unless($wallet->user_id === Auth::id(), 403);
            $this->wallet = $wallet;
        }

        $this->asset = $asset?->exists ? $asset : null;

        if (! in_array($this->sortField, self::ALLOWED_SORT_FIELDS, true)) {
            $this->sortField = 'date';
        }

        if (! in_array($this->sortDirection, ['asc', 'desc'], true)) {
            $this->sortDirection = 'desc';
        }
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, self::ALLOWED_SORT_FIELDS, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function deleteTransaction(int $id): void
    {
        $transaction = Transaction::query()
            ->where('id', $id)
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if ($transaction === null) {
            session()->flash('error', 'Transaction could not be deleted.');

            return;
        }

        $transaction->delete();

        session()->flash('success', 'Transaction deleted.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    protected function scopeQuery(Builder $query): Builder
    {
        return $query
            ->when($this->wallet !== null, function (Builder $query): void {
                $query->where('wallet_id', $this->wallet->id);
            })
            ->when($this->asset !== null, function (Builder $query): void {
                $query->where('asset_id', $this->asset->id);
            });
    }

    protected function baseQuery(): Builder
    {
        return $this->scopeQuery(Transaction::query())
            ->with(['asset.exchange', 'wallet.broker'])
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->search($this->search)
            ->when($this->dateFrom !== '', function (Builder $query) {
                $query->whereDate('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo !== '', function (Builder $query) {
                $query->whereDate('date', '<=', $this->dateTo);
            });
    }

    protected function applySorting(Builder $query): Builder
    {
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return match ($this->sortField) {
            'asset' => $query
                ->leftJoin('assets as sort_assets', 'sort_assets.id', '=', 'transactions.asset_id')
                ->orderBy('sort_assets.name', $direction)
                ->orderBy('transactions.id')
                ->select('transactions.*'),
            'wallet' => $query
                ->leftJoin('wallets as sort_wallets', 'sort_wallets.id', '=', 'transactions.wallet_id')
                ->orderBy('sort_wallets.name', $direction)
                ->orderBy('transactions.id')
                ->select('transactions.*'),
            'broker' => $query
                ->leftJoin('wallets as sort_wallets', 'sort_wallets.id', '=', 'transactions.wallet_id')
                ->leftJoin('brokers as sort_brokers', 'sort_brokers.id', '=', 'sort_wallets.broker_id')
                ->orderBy('sort_brokers.name', $direction)
                ->orderBy('transactions.id')
                ->select('transactions.*'),
            'price' => $query->orderBy('price_per_unit', $direction)->orderBy('id'),
            default => $query->orderBy($this->sortField, $direction)->orderBy('id'),
        };
    }

    public function render()
    {
        $transactions = $this->applySorting($this->baseQuery())
            ->paginate($this->perPage);

        return view('livewire.my-transactions', [
            'transactions' => $transactions,
            'contextTitle' => $this->wallet?->name ?? $this->asset?->name,
        ]);
    }
}
