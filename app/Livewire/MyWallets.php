<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;
use App\Services\MarketData\StockPriceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MyWallets extends Component
{
    public $price = [];
    protected $listeners = ['transactionSaved' => 'reloadWallets', 'walletLedgerSaved' => 'reloadWallets'];

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
        $this->stockPriceService = $stockPriceService;
        $this->reloadWallets();
    }

    public function reloadWallets(): void
    {
        $relations = ['broker', 'transactions.asset.exchange'];

        if (Schema::hasTable('wallet_ledgers')) {
            $relations[] = 'ledgers';
        }

        $this->wallets = Wallet::query()
            ->with($relations)
            ->where('user_id', Auth::id())
            ->get();
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
