<?php

namespace App\Livewire\Wallet;

use App\Models\Wallet;
use Livewire\Component;

class Index extends Component
{
    public $wallets;

    public function mount()
    {
        $this->wallets = Wallet::with('transactions.asset.exchange')->get();
    }

    public function render()
    {
        return view('livewire.wallet.index');
    }
}
