<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;

class NewTransaction extends Component
{
    /* TRANSACTION DATA */
    public $wallet;
    public $asset;
    public $type;
    public $currency;
    public $quantity;
    public $price_per_unit;
    public $total_value;
    public $total_fees = 0;
    public $date;
    public $notes;

    public function updated($field)
    {
        if (in_array($field, ['quantity', 'price_per_unit', 'total_fees'])) {
            if ($this->quantity && $this->price_per_unit) {
                $this->total_value = $this->quantity * $this->price_per_unit - $this->total_fees;
            } else {
                $this->total_value = 0;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'wallet' => 'required|string',
            'asset' => 'required|string',
            'type' => 'required|string',
            'currency' => 'required|string|max:3',
            'quantity' => 'required|numeric',
            'price_per_unit' => 'required|numeric',
            'total_fees' => 'required|numeric',
            'total_value' => 'required|numeric',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Transaction::create([
            'wallet' => $this->wallet,
            'asset' => $this->asset,
            'type' => $this->type,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'price_per_unit' => $this->price_per_unit,
            'total_fees' => $this->total_fees,
            'total_value' => $this->total_value,
            'date' => $this->date,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Transaction saved!');

        // np. czyścisz formularz:
        $this->reset();
    }


    // new transaction modal 
    public $showModal = false;
    protected $listeners = ['openNewTransactionModal' => 'openModal'];

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
        return view('livewire.new-transaction');
    }
}
