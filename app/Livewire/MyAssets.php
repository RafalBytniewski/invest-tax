<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;

class MyAssets extends Component
{
    public function render()
    {
        $assets = Asset::with('transactions')->get();


        return view('livewire.my-assets', [
            'assets' => $assets
        ]);
    }
}
