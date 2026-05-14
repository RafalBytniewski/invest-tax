<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
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
            ->with('exchange')
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

        $activeAssetIds = Transaction::query()
            ->select('asset_id')
            ->whereHas('wallet', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->groupBy('asset_id')
            ->havingRaw('SUM(quantity) > 0')
            ->pluck('asset_id');

        return view('livewire.asset.index', [
            'assets' => $assets,
            'activeAssets' => $assets->whereIn('id', $activeAssetIds)->values(),
        ]);
    }
}