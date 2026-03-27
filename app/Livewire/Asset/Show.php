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
    public $transactionQuery;
    public $prices;

    protected function query()
    {
        return Transaction::query()
            ->where('asset_id', $this->asset->id)
            ->whereHas('wallet', function ($query) {
                $query->where('user_id', Auth::id());
            });
    }

    public function countAverage(){
        $totalValue = $this->query()->where('type','buy')->sum('total_value');
        $quantity = $this->query()->where('type','buy')->sum('quantity');
        return $totalValue / $quantity;
    }

    public function mount(Asset $asset)
    {
        $this->quantity = $this->query()->sum('quantity');
        $this->average = $this->countAverage();
        $this->transactionCurrency = $this->query()->value('currency');
        $this->latestPrice = $asset->assetPrices()->latest('date')->first();
        $this->prices = $asset->assetPrices()->get();
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
