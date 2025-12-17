<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;

class StockPriceService
{
    protected $apiKeyAlpha;
    protected $urlAlpha;

    protected $apiKeyTwelve;
    protected $urlTwelve;

    protected $apiKeyEodhd;
    protected $urlEodhd;

    public function __construct()
    {
        $this->apiKeyAlpha = config('services.alphavantage.key');
        $this->urlAlpha = 'https://www.alphavantage.co/query/?symbol="NVDA"/?date="2025-12-11"/?apikey="3266195fd4914158966d9be199e4294e"';
        $this->apiKeyEodhd = config('services.eodhd.key');
        $this->urlEodhd = 'https://eodhd.com/api/eod/';
    }

    public function getTodayOpenPrice(string $symbol, string $exchange): ?float
    {
        if ($exchange == 'GPW') {
            $ticker = $symbol . '.WAR';
            $response = Http::get(
            $this->urlEodhd . $ticker,[
                
                'filter' => 'last_close',
                'api_token' => $this->apiKeyEodhd,
                'fmt' =>'json'
            ]);
            $price = $response->json();
        } else {


            $response = Http::get($this->urlAlpha, [
                'function' => 'TIME_SERIES_DAILY',
                'symbol' => $symbol,
                'apikey' => $this->apiKeyAlpha,
            ]);

            if (!$response->ok()) {
                return null;
            }

            $data = $response->json();

            if (!isset($data['Time Series (Daily)'])) {
                return null;
            }

            $today = date('Y-m-d');

            // Jeśli dzisiejsze dane nie są jeszcze dostępne (np. przed otwarciem giełdy), weź ostatni dzień
            if (!isset($data['Time Series (Daily)'][$today])) {
                $dates = array_keys($data['Time Series (Daily)']);
                $today = $dates[0];
            }

            $price = floatval($data['Time Series (Daily)'][$today]['1. open']);
        }
        return $price;
    }
}
