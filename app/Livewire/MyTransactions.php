<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Broker;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;

class MyTransactions extends Component
{
    use WithPagination;
    // paginate
    public $perPage = '10';
    
    //columns
    public $columns = [
        'asset' => true,
        'broker' => true,
        'wallet' => true,
        'type' => true,
        'quantity' => true,
        'price' => true,
        'total_value' => true,
        'date' => true,
    ];
    protected array $requiredColumns = ['asset', 'wallet', 'type', 'total_value', 'date'];

    // searching feature
    public $search = '';

    // filter by date
    public $dateFrom = '';
    public $dateTo = '';

    // sorting feature
    public $sortField = 'date';
    public $sortDirection = 'asc';
    protected $queryString = ['sortField', 'sortDirection'];
    public array $selected = [];
    public bool $selectAll = false;

    public function sortBy($field)
    {
        $allowedSortFields = ['asset', 'broker', 'wallet', 'type', 'quantity', 'price', 'total_value', 'date'];
        if (! in_array($field, $allowedSortFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedColumns(): void
    {
        foreach ($this->requiredColumns as $column) {
            $this->columns[$column] = true;
        }
    }

    public function updatedSelectAll($value): void
    {
        $displayed = $this->getDisplayedTransactionIds();
        $checked = (bool) $value;

        foreach ($displayed as $id) {
            $this->selected[$id] = $checked;
        }
    }

    public function updatedSelected(): void
    {
        $displayed = $this->getDisplayedTransactionIds();
        $selectedIds = $this->getSelectedIds();
        $this->selectAll = ! empty($displayed) && empty(array_diff($displayed, $selectedIds));
    }

    public function deleteSelected(): void
    {
        $ids = array_map('intval', $this->getSelectedIds());
        if ($ids === []) {
            return;
        }

        $deletedCount = Transaction::query()
            ->whereIn('id', $ids)
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->delete();

        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();

        session()->flash('success', "Deleted {$deletedCount} selected transaction(s).");
    }

    public function updatingSearch(): void
    {
        $this->resetPageAndSelection();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPageAndSelection();
    }

    public function updatingDateTo(): void
    {
        $this->resetPageAndSelection();
    }

    public function updatingPerPage(): void
    {
        $this->resetPageAndSelection();
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
            'asset' => $query->orderBy(
                Asset::select('name')
                    ->whereColumn('assets.id', 'transactions.asset_id')
                    ->limit(1),
                $direction
            ),
            'wallet' => $query->orderBy(
                Wallet::select('name')
                    ->whereColumn('wallets.id', 'transactions.wallet_id')
                    ->limit(1),
                $direction
            ),
            'broker' => $query->orderBy(
                Broker::select('name')
                    ->join('wallets', 'wallets.broker_id', '=', 'brokers.id')
                    ->whereColumn('wallets.id', 'transactions.wallet_id')
                    ->limit(1),
                $direction
            ),
            'price' => $query->orderBy('price_per_unit', $direction),
            default => $query->orderBy($this->sortField, $direction),
        };
    }

    protected function resetPageAndSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    protected function getDisplayedTransactionIds(): array
    {
        return $this->applySorting($this->baseQuery())
            ->paginate((int) $this->perPage, ['*'], 'page', $this->getPage())
            ->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->getSelectedIds());
    }

    protected function getSelectedIds(): array
    {
        return collect($this->selected)
            ->filter(fn ($checked) => (bool) $checked)
            ->keys()
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
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
