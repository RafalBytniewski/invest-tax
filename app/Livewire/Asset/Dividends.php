<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class Dividends extends Component
{
    use WithPagination;

    public Asset $asset;

    public function mount(Asset $asset): void
    {
        $this->asset = $asset->load('exchange');
    }

    public function render()
    {
        $dividends = $this->asset->dividends()
            ->orderByDesc('ex_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.asset.dividends', [
            'dividends' => $dividends,
        ]);
    }
}
