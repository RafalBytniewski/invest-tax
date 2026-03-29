<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;

class Assets extends Component
{
    public $type = null;
    public $exchange = null;
    public $region = null;

    public function resetFilters(){
        $this->type = null;
        $this->exchange = null;
        $this->region = null;
    }

    public function render()
    {
        $assets = Asset::orderBy('name')
            ->when($this->type, function ($query) {
                $query->where('asset_type', $this->type);
            })
            ->when($this->exchange, function ($query) {
                $query->whereHas('exchange', function ($q) {
                    $q->where('symbol', $this->exchange);
                });
            })
            ->when($this->region, function ($query) {
                $query->whereHas('exchange', function ($q) {
                    $q->where('region', $this->region);
                });
            })
            ->get();
 

        return view('livewire.assets', [
            'assets' => $assets
        ]);
    }
}
