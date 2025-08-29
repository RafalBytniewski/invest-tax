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
    // paginate
    public $perPage = '10';

    // searching feature
    public $search = '';
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // sorting feature
    public $sortField = 'type';
    public $sortDirection = 'asc';
    protected $queryString = ['sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }


    // bulk actions 
    public $selected = [];   // zaznaczone ID
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Transaction::pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    // new transaction modal 
    public $showModal = false;

    public function openModal()
    {
        $this->showModal = true;
    } 

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        $transactions = Transaction::search( $this->search)->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.my-transactions', [
            'transactions' => $transactions,
        ]);
    }
}
