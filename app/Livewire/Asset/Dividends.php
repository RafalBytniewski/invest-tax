<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Dividend;
use Illuminate\Contracts\View\View;
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

    public function render(): View
    {
        $dividends = $this->asset->dividends()
            ->orderByDesc('ex_date')
            ->orderByDesc('id')
            ->paginate(10);

        $dividendChartData = $this->asset->dividends()
            ->orderBy('ex_date')
            ->orderBy('id')
            ->get()
            ->map(fn (Dividend $dividend): array => [
                'date' => $dividend->ex_date->format('Y-m-d'),
                'amount' => (float) $dividend->amount,
            ])
            ->values()
            ->all();

        return view('livewire.asset.dividends', [
            'dividends' => $dividends,
            'dividendChartData' => $dividendChartData,
        ]);
    }
}
