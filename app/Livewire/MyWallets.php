<?php

namespace App\Livewire;

use App\Models\Wallet;
use App\Services\MarketData\StockPriceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyWallets extends Component
{
    public $price = [];
    public $wallets;
    public array $summary = [];
    public array $visibleTransactions = [];

    public function loadPrice(
        StockPriceService $stockPriceService,
        $symbol,
        $exchange
    ) {
        $this->price[$symbol] = $stockPriceService->getTodayOpenPrice($symbol, $exchange);
    }

    public function mount()
    {
        $wallets = Wallet::query()
            ->with([
                'broker',
                'transactions' => fn ($query) => $query->orderByDesc('date'),
                'transactions.asset.exchange',
            ])
            ->where('user_id', Auth::id())
            ->get();

        $this->wallets = $wallets->map(function (Wallet $wallet) {
            $transactions = $wallet->transactions->sortByDesc('date')->values();
            $activeAssets = $this->activeAssetsForWallet($wallet);
            $invested = (float) $transactions->sum('total_value');
            $lastTransaction = $transactions->first();

            $wallet->setAttribute('active_assets_count', $activeAssets->count());
            $wallet->setAttribute('transactions_count', $transactions->count());
            $wallet->setAttribute('invested_total', $invested);
            $wallet->setAttribute('last_transaction_date', $lastTransaction?->date?->format('d.m.Y'));
            $wallet->setAttribute('recent_transactions', $transactions->take(10));
            $wallet->setAttribute('chart_data', $activeAssets->map(function ($asset) use ($transactions) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'amount' => (float) $transactions
                        ->where('asset_id', $asset->id)
                        ->sum('total_value'),
                ];
            })->values());

            return $wallet;
        });

        $currencyBreakdown = $this->wallets
            ->groupBy('currency')
            ->map(fn ($items, $currency) => $currency . ': ' . $items->count())
            ->values()
            ->implode(' / ');

        $brokerBreakdown = $this->wallets
            ->groupBy(fn ($wallet) => $wallet->broker?->name ?? 'No broker')
            ->map(fn ($items, $broker) => $broker . ': ' . $items->count())
            ->values()
            ->implode(' / ');

        $lastActivity = $this->wallets
            ->flatMap(fn ($wallet) => $wallet->transactions)
            ->sortByDesc('date')
            ->first();

        $largestWallet = $this->wallets->sortByDesc('invested_total')->first();

        $this->summary = [
            'wallets_count' => $this->wallets->count(),
            'transactions_count' => $this->wallets->sum('transactions_count'),
            'active_assets_count' => $this->wallets
                ->flatMap(fn ($wallet) => $this->activeAssetsForWallet($wallet)->pluck('id'))
                ->unique()
                ->count(),
            'realized_profit' => (float) $this->wallets->sum(fn ($wallet) => round($wallet->realizedPL(), 2)),
            'currencies' => $currencyBreakdown !== '' ? $currencyBreakdown : 'No wallets yet',
            'brokers' => $brokerBreakdown !== '' ? $brokerBreakdown : 'No brokers yet',
            'largest_wallet' => $largestWallet?->name ?? 'No wallets yet',
            'largest_wallet_total' => (float) ($largestWallet?->invested_total ?? 0),
            'largest_wallet_currency' => $largestWallet?->currency ?? '',
            'last_activity' => $lastActivity?->date?->format('d.m.Y') ?? 'No activity yet',
        ];
    }

    protected function activeAssetsForWallet(Wallet $wallet)
    {
        return $wallet->transactions
            ->groupBy('asset_id')
            ->filter(fn ($transactions) => $transactions->sum('quantity') > 0)
            ->map(fn ($transactions) => $transactions->first()->asset)
            ->filter()
            ->values();
    }

    public function toggleTransactions($assetId)
    {
        if (isset($this->visibleTransactions[$assetId])) {
            unset($this->visibleTransactions[$assetId]);
        } else {
            $this->visibleTransactions[$assetId] = true;
        }
    }

    public function render()
    {
        return view('livewire.my-wallets');
    }
}
