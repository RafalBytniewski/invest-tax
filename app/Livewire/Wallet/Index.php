<?php

namespace App\Livewire\Wallet;

use App\Services\Wallet\WalletOverviewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public array $wallets = [];

    public function mount(WalletOverviewService $walletOverviewService): void
    {
        $this->wallets = $walletOverviewService->forUser(Auth::user());
    }

    public function render()
    {
        return view('livewire.wallet.index');
    }
}
