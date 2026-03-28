<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Asset $asset;

    public $quantity;
    public $average;
    public $latestPrice;
    public $transactionCurrency;
    public $assetSymbol;

    public $realizedPL;
    public $totalValue;
    
    protected function GetTVAssetSymbol(Asset $asset)
    {
        if ($asset->asset_type === 'crypto') {
            $this->assetSymbol = 'BINANCE:' . $asset->symbol . 'USD';
        }else{
            $this->assetSymbol = $asset->exchange->symbol . ':' . $asset->symbol;
        }
        }
    protected function query()
    {
        return Transaction::query()
            ->where('asset_id', $this->asset->id)
            ->whereHas('wallet', function ($query) {
                $query->where('user_id', Auth::id());
            });
    }

    public function countAverage()
    {
        $totalValue = $this->query()->where('type', 'buy')->sum('total_value');
        $quantity = $this->query()->where('type', 'buy')->sum('quantity');
        if($quantity != 0){
            return $totalValue / $quantity;
        }else{
            return '-';
        }
    }

    public function countRealizedPL(){
        $totalValue = $this->query()->where('type', 'sell')->sum('total_value');
        $quantity = $this->query()->where('type', 'sell')->sum('quantity');
        if($quantity != 0){
        $averageSell = $totalValue/$quantity;
            return (abs($averageSell)-$this->countAverage())*abs($quantity);
         }else{
            return '-';
        }
    }

    public function mount(Asset $asset)
    {
        $this->quantity = $this->query()->sum('quantity');
        $this->average = $this->countAverage();
        $this->transactionCurrency = $this->query()->value('currency'); // check wallet->currency
        $this->latestPrice = $asset->assetPrices()->latest('date')->first();
        $this->GetTVAssetSymbol($asset);
        
        $this->realizedPL = $this->countRealizedPL();
    }
    public function render()
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
