<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wallet;

class MyWallets extends Component
{
    public function render()
    {
        $wallets = Wallet::with('transactions')->get();

        return view('livewire.my-wallets',[
            'wallets' => $wallets
        ]);
    }
}
