<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Livewire\Attributes\Reactive;

class MyTransactions extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'type';
    public $sortDirection = 'asc';

    protected $queryString = ['sortField', 'sortDirection'];
    
    public function sortBy($field){
        $this->sortDirection = $this->sortField === $field ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc' :'asc';
        
        $this->sortField = $field;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $transactions = Transaction::search(['asset.name', 'asset.exchange.name', 'wallet.name', 'type'], $this->search)->orderBy($this->sortField, $this->sortDirection)->paginate(10);

        return view('livewire.my-transactions', [
            'transactions' => $transactions,
        ]);
    }
}
