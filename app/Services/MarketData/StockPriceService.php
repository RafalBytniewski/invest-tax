<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;

class StockPriceService
{
    protected string $apiKeyAlpha;

    protected string $urlAlpha;

    protected string $apiKeyEodhd;

    protected string $urlEodhd;

    public function __construct()
    {
        $this->apiKeyAlpha = (string) config('services.alphavantage.key');
        $this->urlAlpha = 'https://www.alphavantage.co/query';
        $this->apiKeyEodhd = (string) config('services.eodhd.key');
        $this->urlEodhd = 'https://eodhd.com/api/eod/';
    }

    public function getTodayOpenPrice(string $symbol, ?string $exchange): ?float
    {
        return $exchange === 'GPW'
            ? $this->getGpwClosePrice($symbol)
            : $this->getLatestOpenPriceFromAlphaVantage($symbol);
    }

    protected function getGpwClosePrice(string $symbol): ?float
    {
        if ($this->apiKeyEodhd === '') {
            return null;
        }

        $response = Http::get($this->urlEodhd.$symbol.'.WAR', [
            'filter' => 'last_close',
            'api_token' => $this->apiKeyEodhd,
            'fmt' => 'json',
        ]);

        if (! $response->ok()) {
            return null;
        }

        $price = $response->json();

        return is_numeric($price) ? (float) $price : null;
    }

    protected function getLatestOpenPriceFromAlphaVantage(string $symbol): ?float
    {
        if ($this->apiKeyAlpha === '') {
            return null;
        }

        $response = Http::get($this->urlAlpha, [
            'function' => 'TIME_SERIES_DAILY',
            'symbol' => $symbol,
            'apikey' => $this->apiKeyAlpha,
        ]);

        if (! $response->ok()) {
            return null;
        }

        $series = data_get($response->json(), 'Time Series (Daily)');

        if (! is_array($series) || $series === []) {
            return null;
        }

        $latestDate = array_key_first($series);
        $price = data_get($series, $latestDate.'.1. open');

        return is_numeric($price) ? (float) $price : null;
    }
}
