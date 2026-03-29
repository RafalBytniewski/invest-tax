<?php

namespace App\Livewire;

use App\Models\Wallet;
use App\Services\MarketData\StockPriceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyWallets extends Component
{
    public array $price = [];

    public $wallets;

    public array $visibleTransactions = [];

    public function mount(): void
    {
        $this->wallets = Wallet::query()
            ->with(['broker', 'transactions.asset.exchange'])
            ->where('user_id', Auth::id())
            ->get();
    }

    public function loadPrice(StockPriceService $stockPriceService, string $symbol, ?string $exchange): void
    {
        $this->price[$symbol] = $stockPriceService->getTodayOpenPrice($symbol, $exchange);
    }

    public function toggleTransactions(int $assetId): void
    {
        $this->visibleTransactions[$assetId] = ! ($this->visibleTransactions[$assetId] ?? false);
    }

    public function render()
    {
        return view('livewire.my-wallets');
    }
}
