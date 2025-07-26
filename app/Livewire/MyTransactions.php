<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class MyTransactions extends Component
{
    use WithPagination;

    public $search = 'sell';

    public function render()
    {
        $transactions = Transaction::search('type', $this->search)->paginate(10);

        return view('livewire.my-transactions', [
            'transactions' => $transactions,
        ]);
    }
}
