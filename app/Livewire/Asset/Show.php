<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Asset $asset;

    public function render()
    {
        $transactions = Transaction::query()
            ->with('wallet.broker')
            ->where('asset_id', $this->asset->id)
            ->whereHas('wallet', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest('date')
            ->limit(10)
            ->get();

        return view('livewire.asset.show', [
            'transactions' => $transactions,
        ]);
    }
}
