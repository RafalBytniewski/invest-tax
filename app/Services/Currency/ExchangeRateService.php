<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
public function getLastCurrencyPrice($symbol)
    {
        $url = "https://api.nbp.pl/api/exchangerates/rates/a/{$symbol}/last/1/?format=json";

        $response = Http::get($url);

        if (!$response->ok()) {
            return null;
        }
        $data = $response->json();
        $price =  $data['rates'][0]['mid'];
        dd($price);
        
    }
    public function getCurrencyPrice($symbol, $date){

    }
}