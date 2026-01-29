<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;

class MyAssets extends Component
{
    public function render()
{
    $assets = Asset::with([
        'transactions',
        'assetPrices' => function ($q) {
            $q->orderBy('date', 'asc'); // dodaj kierunek sortowania
        }
    ])->get();

    return view('livewire.my-assets', [
        'assets' => $assets
    ]);
}
}

