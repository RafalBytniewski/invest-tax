<?php

namespace App\Livewire\MyWallet;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Wallet $wallet;

    public function mount(Wallet $wallet): void
    {
        abort_unless($wallet->user_id === Auth::id(), 403);

        $wallet->load([
            'broker',
            'transactions' => fn ($query) => $query
                ->with(['asset.exchange'])
                ->latest('date'),
        ]);

        $wallet->setAttribute('active_assets_count', $wallet->activeAssetsCollection()->count());
        $wallet->setAttribute('transactions_count', $wallet->transactions->count());
        $wallet->setAttribute('invested_total', $wallet->transactions->where('type', 'buy')->sum('total_value'));
        $wallet->setAttribute('last_transaction_date', $wallet->transactions->first()?->date?->format('d.m.Y'));

        $this->wallet = $wallet;
    }

    public function render()
    {
        return view('livewire.my-wallet.show', [
            'transactions' => $this->wallet->transactions->take(10),
        ]);
    }
}