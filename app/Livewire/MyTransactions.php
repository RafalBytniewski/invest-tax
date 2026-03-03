<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Broker;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyTransactions extends Component
{
    use WithPagination;

    protected array $columnConfig = [
        'asset' => ['label' => 'Asset', 'required' => true, 'sortable' => true],
        'broker' => ['label' => 'Broker', 'required' => false, 'sortable' => true],
        'wallet' => ['label' => 'Wallet', 'required' => true, 'sortable' => true],
        'type' => ['label' => 'Type', 'required' => true, 'sortable' => true],
        'quantity' => ['label' => 'Quantity', 'required' => false, 'sortable' => true],
        'price' => ['label' => 'Price', 'required' => false, 'sortable' => true],
        'total_value' => ['label' => 'Total Value', 'required' => true, 'sortable' => true],
        'date' => ['label' => 'Date', 'required' => true, 'sortable' => true],
    ];

    public $perPage = '10';
    public array $columns = [];
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    public $sortField = 'date';
    public $sortDirection = 'asc';
    protected $queryString = ['sortField', 'sortDirection'];

    public array $selected = [];
    public array $displayedIds = [];
    public bool $selectAll = false;

    public function mount(): void
    {
        $this->columns = collect($this->columnConfig)
            ->mapWithKeys(fn (array $config, string $key) => [$key => true])
            ->all();

        foreach ($this->lockedColumns as $key) {
            $this->columns[$key] = true;
        }
    }

    public function getColumnOptionsProperty(): array
    {
        return $this->columnConfig;
    }

    public function getLockedColumnsProperty(): array
    {
        return collect($this->columnConfig)
            ->filter(fn (array $config) => $config['required'])
            ->keys()
            ->all();
    }

    public function sortBy(string $field): void
    {
        if (! isset($this->columnConfig[$field]) || ! $this->columnConfig[$field]['sortable']) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPageAndSelection();
    }

    public function updatedColumns(): void
    {
        foreach ($this->lockedColumns as $column) {
            $this->columns[$column] = true;
        }
    }

    public function updatedSelectAll($value): void
    {
        $checked = (bool) $value;

        foreach ($this->displayedIds as $id) {
            $this->selected[$id] = $checked;
        }
    }

    public function updatedSelected(): void
    {
        $selectedIds = $this->getSelectedIds();
        $this->selectAll = ! empty($this->displayedIds) && empty(array_diff($this->displayedIds, $selectedIds));
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

    public function deleteTransaction(int $id): void
    {
        Transaction::query()
            ->where('id', $id)
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->delete();

        $this->selected[(string) $id] = false;
        $this->selectAll = false;
        session()->flash('success', 'Transaction deleted.');
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

    public function getSelectedCountProperty(): int
    {
        return count($this->getSelectedIds());
    }

    protected function baseQuery(): Builder
    {
        return Transaction::query()
            ->with(['asset.exchange', 'wallet.broker'])
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            })
            ->when(trim((string) $this->search) !== '', function (Builder $query) {
                $query->search($this->search);
            })
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

    protected function getSelectedIds(): array
    {
        return collect($this->selected)
            ->filter(fn ($checked) => (bool) $checked)
            ->keys()
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    protected function validateDateRange(): bool
    {
        if ($this->dateFrom !== '' && $this->dateTo !== '' && $this->dateFrom > $this->dateTo) {
            $this->addError('dateFrom', 'Date from cannot be later than date to.');
            $this->addError('dateTo', 'Date to cannot be earlier than date from.');

            return false;
        }

        $this->resetErrorBag(['dateFrom', 'dateTo']);

        return true;
    }

    public function render()
    {
        if (! $this->validateDateRange()) {
            $transactions = Transaction::query()->whereRaw('1 = 0')->paginate((int) $this->perPage);
            $this->displayedIds = [];
            $this->selectAll = false;

            return view('livewire.my-transactions', [
                'transactions' => $transactions,
                'columnOptions' => $this->columnOptions,
            ]);
        }

        $transactions = $this->applySorting($this->baseQuery())
            ->paginate((int) $this->perPage);

        $this->displayedIds = $transactions->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        $selectedIds = $this->getSelectedIds();
        $this->selectAll = ! empty($this->displayedIds) && empty(array_diff($this->displayedIds, $selectedIds));

        return view('livewire.my-transactions', [
            'transactions' => $transactions,
            'columnOptions' => $this->columnOptions,
        ]);
    }
}
