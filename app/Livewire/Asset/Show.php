<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use App\Services\Asset\AssetCalculator;
use App\Services\Currency\ExchangeRateService;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Component;

class Show extends Component
{
    public Asset $asset;

    public $assetSymbol;

    public float $quantity = 0;

    public ?float $average = null;

    public float $costBasis = 0;

    public $latestPrice = null;

    public ?string $walletCurrency = null;

    public ?string $assetCurrency = null;

    public float $realizedPL = 0;

    public ?float $currentPL = null;

    public ?float $positionValue = null;

    public int $sellTransaction = 0;

    public int $buyTransaction = 0;

    protected function getTVAssetSymbol(Asset $asset): void
    {
        $this->assetSymbol = $asset->asset_type === 'crypto'
            ? 'BINANCE:'.$asset->symbol.'USD'
            : $asset->exchange?->symbol.':'.$asset->symbol;
    }

    public function mount(Asset $asset, AssetCalculator $calculator, ExchangeRateService $rate): void
    {
        $this->asset = $asset->loadMissing('exchange');
        $this->getTVAssetSymbol($this->asset);

        $transactions = Transaction::forUserAssets(Auth::id(), $this->asset->id)
            ->whereIn('type', ['buy', 'sell'])
            ->with('wallet:id,currency')
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        $currencies = $transactions
            ->pluck('currency')
            ->filter()
            ->map(fn ($currency) => strtoupper((string) $currency))
            ->unique()
            ->values();

        // Amounts in different currencies cannot be added without converting every
        // transaction at its historical rate.
        if ($currencies->count() > 1) {
            $this->addError('currency', 'This asset has transactions in multiple currencies. Split them by currency or normalize them before calculating the position.');

            return;
        }

        try {
            $summary = $calculator->calculate($transactions);
        } catch (InvalidArgumentException $exception) {
            $this->addError('transactions', $exception->getMessage());

            return;
        }

        $this->quantity = $summary['quantity'];
        $this->costBasis = $summary['cost_basis'];
        $this->average = $summary['average'];
        $this->realizedPL = $summary['realized_pl'];
        $this->buyTransaction = $summary['buy_count'];
        $this->sellTransaction = $summary['sell_count'];
        $this->walletCurrency = $currencies->first()
            ?? strtoupper((string) $transactions->first()->wallet?->currency)
            ?: null;
        $this->assetCurrency = $this->asset->asset_type === 'crypto'
            ? 'USD'
            : $this->asset->exchange?->currency;

        if ($this->quantity <= 0) {
            $this->positionValue = 0;
            $this->currentPL = 0;

            return;
        }

        $this->latestPrice = $this->asset->assetPrices()->latest('date')->first();

        if ($this->latestPrice === null) {
            return;
        }

        $marketPrice = (float) $this->latestPrice->close_price;

        if ($this->walletCurrency === $this->assetCurrency) {
            $this->positionValue = $calculator->positionValue($marketPrice, $this->quantity);
        } elseif ($this->walletCurrency === 'PLN' && $this->assetCurrency !== null) {
            $currencyRate = $rate->getCurrencyPrice($this->assetCurrency, $this->latestPrice->date);
            $this->positionValue = $currencyRate === null
                ? null
                : $calculator->positionValue($marketPrice * $currencyRate, $this->quantity);
        }

        $this->currentPL = $calculator->unrealizedPL($this->positionValue, $this->costBasis);
    }

    public function render()
    {
        $transactions = Transaction::forUserAssets(Auth::id(), $this->asset->id)
            ->with(['wallet.broker'])
            ->latest('date')
            ->limit(10)
            ->get();

        return view('livewire.asset.show', [
            'transactions' => $transactions,
        ]);
    }
}
