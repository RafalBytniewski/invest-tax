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

    public $perPage = 10;
    public $type = '';
    public $search = '';
    public $selected = [];
    public $selectAll = false;
    public $sortField = 'date';
    public $sortDirection = 'desc';

    public $columnVisibility = [
        'asset' => true,
        'broker' => true,
        'wallet' => true,
        'type' => true,
        'quantity' => true,
        'price' => true,
        'total_value' => true,
        'date' => true,
    ];

    protected $queryString = [
        'sortField',
        'sortDirection',
        'type' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->perPage = (int) $this->perPage;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setType(string $type = ''): void
    {
        $this->type = $type;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->type = '';
        $this->sortField = 'date';
        $this->sortDirection = 'desc';
        $this->resetPage();
        $this->clearSelection();
    }

    public function sortBy(string $field): void
    {
        if (!in_array($field, $this->sortableFields(), true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->clearSelection();
    }


    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->baseQuery()->pluck('transactions.id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function visibleColumnCount(): int
    {
        return collect($this->columnVisibility)->filter()->count();
    }

    protected function sortableFields(): array
    {
        return ['asset', 'broker', 'wallet', 'type', 'quantity', 'total_value', 'date'];
    }

    protected function baseQuery(): Builder
    {
        return Transaction::query()
            ->with(['asset.exchange', 'wallet.broker'])
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->search($this->search)
            ->when($this->type !== '', function (Builder $query) {
                $query->where('type', $this->type);
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
