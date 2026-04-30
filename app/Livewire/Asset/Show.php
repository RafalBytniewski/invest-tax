<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use App\Services\Asset\AssetCalculator;
use App\Services\Currency\ExchangeRateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Asset $asset;
    public $assetSymbol;

    public $quantity;
    public $average;
    public $totalValue;
    public $latestPrice;

    public $walletCurrency;
    public $assetCurrency;

    public $realizedPL = 0;
    public $currentPL = 0;
    public $positionValue;

    public $sellTransaction;
    public $buyTransaction;

    /*     public function countRealizedPL()
    {
        $totalValue = Transaction::forUserAssets(Auth::id(), $this->asset->id)->where('type', 'sell')->sum('total_value');
        $quantity = $this->query()->where('type', 'sell')->sum('quantity');
        if ($quantity != 0) {
            $averageSell = $totalValue / $quantity;
            return (abs($averageSell) - $this->countAverage()) * abs($quantity);
        } else {
            return '-';
        }
    } */

    protected function getTVAssetSymbol(Asset $asset)
    {
        if ($asset->asset_type === 'crypto') {
            $this->assetSymbol = 'BINANCE:' . $asset->symbol . 'USD';
        } else {
            $this->assetSymbol = $asset->exchange->symbol . ':' . $asset->symbol;
        }
    }

    public function mount(Asset $asset, AssetCalculator $calculator, ExchangeRateService $rate)
    {
        $this->getTVAssetSymbol($asset);
        
        $data = Transaction::forUserAssets(Auth::id(), $this->asset->id)
            ->selectRaw('
                SUM(CASE WHEN type = "buy" THEN total_value ELSE 0 END) as buy_value,
                SUM(CASE WHEN type = "buy" THEN quantity ELSE 0 END) as buy_qty,
                SUM(CASE WHEN type = "sell" THEN total_value ELSE 0 END) as sell_value,
                SUM(CASE WHEN type = "sell" THEN quantity ELSE 0 END) as sell_qty,
                COUNT(CASE WHEN type = "buy" THEN 1 END) as buy_transaction,
                COUNT(CASE WHEN type = "sell" THEN 1 END) as sell_transaction
            ')->first();
        if (($data->buy_qty + $data->sell_qty) == 0) {
            $this->average = '-';
            $this->quantity = 0;
            $this->sellTransaction = 0;
            $this->buyTransaction = 0;
            $this->positionValue = null;
            return;
        }
        $this->sellTransaction = $data->sell_transaction;
        $this->buyTransaction = $data->buy_transaction;

        $this->average = $calculator->average($data->buy_value, $data->buy_qty);
        $this->quantity = $data->buy_qty + $data->sell_qty;

        $this->latestPrice = $asset->assetPrices()->latest('date')->first();

        $this->walletCurrency = Transaction::forUserAssets(Auth::id(), $this->asset->id)
            ->join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
            ->value('wallets.currency');
        $this->assetCurrency = $asset->exchange->currency;

        if ($this->latestPrice !== null) {
            if ($this->walletCurrency == $this->assetCurrency) {
                $this->positionValue = $calculator->positionValue($this->latestPrice->close_price, $this->quantity);
            } else {
                $currencyRate = $rate->getCurrencyPrice($this->assetCurrency, $this->latestPrice->date);
                if($currencyRate !== null){ 
                    $this->positionValue = $currencyRate * ($calculator->positionValue($this->latestPrice->close_price, $this->quantity));
                }else{
                    $this->positionValue = '-';
                }
            }
        } else {
            $this->positionValue = null;
        }

        $this->currentPL = $this->positionValue - ($this->average*$this->quantity);
        $this->realizedPL = $calculator->realizedPL($data->sell_value, $data->sell_qty, $this->average);
    }

    public function render()
    {
        $transactions = Transaction::forUserAssets(Auth::id(), $this->asset->id)
            ->latest('date')
            ->limit(10)
            ->get();

        return view('livewire.asset.show', [
            'transactions' => $transactions,
        ]);
    }
}
