<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;

class MyWallets extends Component
{
/*     public $chartLabels = [];
    public $chartData = [];
 */

    public $wallets;
    public $visibleTransactions = [];

    public function mount()
    {
        $this->wallets = Wallet::with('transactions.asset.exchange')->get();

        /* $this->loadChartData(); */
    }
/* 
    public function loadChartData()
    {
        // przykładowe dane do testu wykresu
        $this->chartLabels = ['Bitcoin', 'Ethereum', 'AAPL', 'TSLA'];
        $this->chartData = [4500, 2500, 3000, 2000]; // wartości w USD
    } */

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
