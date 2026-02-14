<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use App\Services\MarketData\StockPriceService;

class MyWallets extends Component
{
    public $price = [];

    protected $stockPriceService;

    public function loadPrice(
        StockPriceService $stockPriceService,
        $symbol,
        $exchange
    ) {
        $this->price[$symbol] = $stockPriceService->getTodayOpenPrice($symbol, $exchange);
        $this->walletSummaries = $this->buildWalletSummaries();
    }


    public $wallets;
    public $walletSummaries = [];
    public $visibleTransactions = [];

    public function mount(StockPriceService $stockPriceService)
    {
        $this->wallets = Wallet::with(['broker', 'transactions.asset.exchange'])->get();
        $this->stockPriceService = $stockPriceService;
        $this->walletSummaries = $this->buildWalletSummaries();
    }


    public function toggleTransactions($walletId, $assetId)
    {
        $key = $this->transactionToggleKey($walletId, $assetId);

        if (isset($this->visibleTransactions[$key])) {
            unset($this->visibleTransactions[$key]);
        } else {
            $this->visibleTransactions[$key] = true;
        }
    }

    public function isTransactionsVisible($walletId, $assetId): bool
    {
        return isset($this->visibleTransactions[$this->transactionToggleKey($walletId, $assetId)]);
    }

    protected function transactionToggleKey($walletId, $assetId): string
    {
        return $walletId . ':' . $assetId;
    }

    protected function buildWalletSummaries(): array
    {
        $summaries = [];

        foreach ($this->wallets as $wallet) {
            $assets = $wallet->transactions
                ->pluck('asset')
                ->filter()
                ->unique('id')
                ->values();

            $assetSummaries = $assets->map(function ($asset) use ($wallet) {
                $transactions = $wallet->transactions
                    ->where('asset_id', $asset->id)
                    ->sortByDesc('date')
                    ->values();

                $quantity = (float) $transactions->sum('quantity');
                $marketPrice = array_key_exists($asset->symbol, $this->price)
                    ? $this->price[$asset->symbol]
                    : null;

                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'symbol' => $asset->symbol,
                    'exchange_symbol' => $asset->exchange?->symbol,
                    'transaction_count' => $transactions->count(),
                    'quantity' => $quantity,
                    'avg_buy_price' => (float) $wallet->averageBuyPrice($asset->id),
                    'market_price' => $marketPrice,
                    'current_value' => $marketPrice !== null ? $marketPrice * $quantity : null,
                    'allocation_value' => (float) $transactions->sum('total_value'),
                    'transactions' => $transactions,
                ];
            })->values();

            $knownAssetValues = $assetSummaries->filter(fn($item) => $item['current_value'] !== null);

            $summaries[$wallet->id] = [
                'asset_count' => $assetSummaries->count(),
                'transaction_count' => $wallet->transactions->count(),
                'invested_capital' => (float) $wallet->transactions
                    ->where('type', 'buy')
                    ->sum(fn($t) => abs((float) $t->total_value)),
                'net_cash_flow' => (float) $wallet->transactions->sum('total_value'),
                'realized_pl' => (float) $wallet->realizedPL(),
                'current_value' => $knownAssetValues->count() > 0 ? (float) $knownAssetValues->sum('current_value') : null,
                'current_value_coverage' => $assetSummaries->count() > 0
                    ? round(($knownAssetValues->count() / $assetSummaries->count()) * 100, 0)
                    : 0,
                'chart_labels' => $assetSummaries->pluck('name')->values()->all(),
                'chart_values' => $assetSummaries->pluck('allocation_value')->values()->all(),
                'assets' => $assetSummaries->all(),
            ];
        }

        return $summaries;
    }

    public function render()
    {
        return view('livewire.my-wallets');
    }
}
