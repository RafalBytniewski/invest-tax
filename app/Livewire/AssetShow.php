<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssetShow extends Component
{
    public Asset $asset;

    public array $chartLabels = [];

    public array $chartValues = [];

    public int $pricePoints = 0;

    public ?array $latestPrice = null;
    
    public ?string $tradingViewSymbol = null;

    public ?string $tradingViewEmbedUrl = null;

    public function mount(Asset $asset): void
    {
        $this->asset = $asset->load('exchange');

        $priceRows = $asset->assetPrices()
            ->whereNotNull('close_price')
            ->orderByDesc('date')
            ->limit(365)
            ->get(['date', 'close_price', 'source'])
            ->sortBy('date')
            ->values();

        $this->chartLabels = $priceRows
            ->map(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'))
            ->all();

        $this->chartValues = $priceRows
            ->map(fn ($row) => (float) $row->close_price)
            ->all();

        $this->pricePoints = count($this->chartValues);

        $latest = $priceRows->last();

        if ($latest !== null) {
            $this->latestPrice = [
                'value' => (float) $latest->close_price,
                'date' => Carbon::parse($latest->date)->format('Y-m-d'),
                'source' => $latest->source,
            ];
        }

        $this->tradingViewSymbol = $this->resolveTradingViewSymbol($asset);
        $this->tradingViewEmbedUrl = $this->tradingViewSymbol
            ? $this->buildTradingViewEmbedUrl($this->tradingViewSymbol)
            : null;
    }

    public function render()
    {
        $transactions = Transaction::query()
            ->with('wallet.broker')
            ->where('asset_id', $this->asset->id)
            ->whereHas('wallet', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest('date')
            ->limit(10)
            ->get();

        return view('livewire.asset-show', [
            'transactions' => $transactions,
        ]);
    }

    protected function resolveTradingViewSymbol(Asset $asset): ?string
    {
        $symbol = strtoupper(trim((string) $asset->symbol));

        if ($symbol === '') {
            return null;
        }

        if ($asset->asset_type === 'crypto') {
            if (str_contains($symbol, 'USDT') || str_contains($symbol, 'USD')) {
                return 'BINANCE:' . $symbol;
            }

            return 'BINANCE:' . $symbol . 'USDT';
        }

        $exchangeSymbol = strtoupper(trim((string) $asset->exchange?->symbol));

        if ($exchangeSymbol === '') {
            return $symbol;
        }

        $exchangeMap = [
            'NASDAQ' => 'NASDAQ',
            'NYSE' => 'NYSE',
            'AMEX' => 'AMEX',
            'ARCA' => 'AMEX',
            'LSE' => 'LSE',
            'XETRA' => 'XETR',
            'XETR' => 'XETR',
            'FWB' => 'FWB',
            'GPW' => 'GPW',
            'TSX' => 'TSX',
            'ASX' => 'ASX',
            'SIX' => 'SIX',
            'BME' => 'BME',
            'EURONEXT' => 'EURONEXT',
        ];

        $tvExchange = $exchangeMap[$exchangeSymbol] ?? $exchangeSymbol;

        return $tvExchange . ':' . $symbol;
    }

    protected function buildTradingViewEmbedUrl(string $symbol): string
    {
        $params = http_build_query([
            'symbol' => $symbol,
            'interval' => 'D',
            'hidesidetoolbar' => 1,
            'symboledit' => 0,
            'saveimage' => 0,
            'toolbarbg' => 'f1f3f6',
            'theme' => 'light',
            'style' => '1',
            'timezone' => 'Etc/UTC',
            'studies' => [],
            'withdateranges' => 1,
            'hideideas' => 1,
        ]);

        return 'https://s.tradingview.com/widgetembed/?' . $params;
    }
}
