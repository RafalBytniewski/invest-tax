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
    }


    public $wallets;
    public $visibleTransactions = [];

    public function mount(StockPriceService $stockPriceService)
    {
        $this->wallets = Wallet::with('transactions.asset.exchange')->get();
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
