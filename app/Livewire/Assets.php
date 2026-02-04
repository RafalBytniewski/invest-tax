<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;

class Assets extends Component
{
    public $type = '';
    public function render()
    {
        $assets = Asset::with('transactions')->orderBy('name')->when($this->type, function ($query) {
            $query->where('asset_type', $this->type);
        })->get();


        return view('livewire.assets', [
            'assets' => $assets
        ]);
    }
}