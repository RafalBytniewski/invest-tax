<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

class MyTransactions extends Component
{
    use WithPagination;

    protected const ALLOWED_SORT_FIELDS = ['asset', 'broker', 'wallet', 'type', 'quantity', 'price', 'total_value', 'date'];
    protected $listeners = ['transactionSaved' => '$refresh'];

    // paginate
    public $perPage = '10';

    // searching feature
    public $search = '';

    // filter by date
    public $dateFrom = '';
    public $dateTo = '';

    // sorting feature
    public $sortField = 'date';
    public $sortDirection = 'asc';
    protected $queryString = ['sortField', 'sortDirection'];

    public function mount(): void
    {
        if (! in_array($this->sortField, self::ALLOWED_SORT_FIELDS, true)) {
            $this->sortField = 'date';
        }

        if (! in_array($this->sortDirection, ['asc', 'desc'], true)) {
            $this->sortDirection = 'asc';
        }
    }

    public function sortBy($field)
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
    }

    public function deleteTransaction(int $id): void
    {
        $deleted = Transaction::query()
            ->where('id', $id)
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->delete();

        if ($deleted === 0) {
            session()->flash('error', 'Transaction could not be deleted.');
            return;
        }

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

    protected function baseQuery(): Builder
    {
        return Transaction::query()
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
                ->select('transactions.*'),
            'wallet' => $query
                ->leftJoin('wallets as sort_wallets', 'sort_wallets.id', '=', 'transactions.wallet_id')
                ->orderBy('sort_wallets.name', $direction)
                ->select('transactions.*'),
            'broker' => $query
                ->leftJoin('wallets as sort_wallets', 'sort_wallets.id', '=', 'transactions.wallet_id')
                ->leftJoin('brokers as sort_brokers', 'sort_brokers.id', '=', 'sort_wallets.broker_id')
                ->orderBy('sort_brokers.name', $direction)
                ->select('transactions.*'),
            'price' => $query->orderBy('price_per_unit', $direction),
            default => $query->orderBy($this->sortField, $direction),
        };
    }

    public function render()
    {
        $transactions = $this->applySorting($this->baseQuery())
            ->paginate($this->perPage);

        return view('livewire.my-transactions', [
            'transactions' => $transactions,
        ]);
    }
}
