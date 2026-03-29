<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Asset $asset;

    public float|int $quantity = 0;

    public float|string $average = '-';

    public $latestPrice = null;

    public ?string $transactionCurrency = null;

    public string $assetSymbol = '';

    public float|string $realizedPL = '-';

    protected function tradingViewSymbol(Asset $asset): string
    {
        if ($asset->asset_type === 'crypto') {
            return 'BINANCE:'.$asset->symbol.'USD';
        }

        return ($asset->exchange?->symbol ?? '').':'.$asset->symbol;
    }

    protected function query(): Builder
    {
        return Transaction::query()
            ->where('asset_id', $this->asset->id)
            ->whereHas('wallet', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public function countAverage(): float|string
    {
        $totalValue = $this->query()->where('type', 'buy')->sum('total_value');
        $quantity = $this->query()->where('type', 'buy')->sum('quantity');

        if ($quantity == 0) {
            return '-';
        }

        return $totalValue / $quantity;
    }

    public function countRealizedPL(): float|string
    {
        $totalValue = $this->query()->where('type', 'sell')->sum('total_value');
        $quantity = $this->query()->where('type', 'sell')->sum('quantity');

        if ($quantity == 0 || ! is_numeric($this->average)) {
            return '-';
        }

        $averageSell = $totalValue / $quantity;

        return (abs($averageSell) - $this->average) * abs($quantity);
    }

    public function mount(Asset $asset): void
    {
        $this->asset = $asset;
        $this->quantity = $this->query()->sum('quantity');
        $this->average = $this->countAverage();
        $this->transactionCurrency = $this->query()->value('currency');
        $this->latestPrice = $asset->assetPrices()->latest('date')->first();
        $this->assetSymbol = $this->tradingViewSymbol($asset);
        $this->realizedPL = $this->countRealizedPL();
    }

    public function render(): View
    {
        $transactions = $this->query()
            ->latest('date')
            ->limit(10)
            ->get();

        return view('livewire.asset.show', [
            'transactions' => $transactions,
        ]);
    }
}
