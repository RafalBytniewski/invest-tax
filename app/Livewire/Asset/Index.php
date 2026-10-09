<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    /**
     * Search all assets by their symbol or name after at least three characters.
     *
     * @return Collection<int, Asset>
     */
    protected function searchAssets(): Collection
    {
        $search = trim($this->search);

        if (mb_strlen($search) < 3) {
            return collect();
        }

        return Asset::query()
            ->with('exchange')
            ->where(function (Builder $query) use ($search): void {
                $query
                    ->where('symbol', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        $userAssetIds = Transaction::query()
            ->select('asset_id')
            ->whereHas('wallet', function (Builder $query): void {
                $query->where('user_id', Auth::id());
            })
            ->distinct()
            ->pluck('asset_id');

        $assets = Asset::query()
            ->whereIn('id', $userAssetIds)
            ->with('exchange')
            ->orderBy('name')
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
            'otherAssets' => $assets->whereNotIn('id', $activeAssetIds)->values(),
            'searchResults' => $this->searchAssets(),
        ]);
    }
}
