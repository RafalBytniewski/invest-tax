<?php

namespace App\Livewire\Wallet;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $wallets;

    public function mount(): void
    {
        $this->wallets = Wallet::query()
            ->where('user_id', Auth::id())
            ->with([
                'broker',
                'walletSnapshots' => fn ($query) => $query
                    ->where('snapshot_type', 'daily')
                    ->oldest('snapshot_at'),
            ])
            ->get();

        $this->wallets->each(function (Wallet $wallet): void {
            $wallet->setAttribute('chart_data', $wallet->walletSnapshots->map(fn ($snapshot): array => [
                'date' => $snapshot->snapshot_at->toDateString(),
                'value' => (float) $snapshot->total_value,
            ])->values()->all());
        });
    }

    public function render()
    {
        return view('livewire.wallet.index');
    }
}
