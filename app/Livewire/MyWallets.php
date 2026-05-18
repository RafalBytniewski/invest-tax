<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use App\Services\MarketData\StockPriceService;
use Illuminate\Support\Facades\Auth;

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
    }


    public $wallets;
    public $visibleTransactions = [];

    public function mount(StockPriceService $stockPriceService)
    {
        $this->wallets = Wallet::with(['broker', 'transactions.asset.exchange'])
            ->where('user_id', Auth::id())
            ->get()
            ->map(function (Wallet $wallet) {
                $wallet->setAttribute('active_assets_count', $wallet->activeAssetsCollection()->count());
                $wallet->setAttribute('transactions_count', $wallet->transactions->count());
                $wallet->setAttribute('invested_total', $wallet->transactions->where('type', 'buy')->sum('total_value'));
                $wallet->setAttribute('last_transaction_date', $wallet->transactions->sortByDesc('date')->first()?->date?->format('d.m.Y'));

                return $wallet;
            });

        $this->stockPriceService = $stockPriceService;
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
