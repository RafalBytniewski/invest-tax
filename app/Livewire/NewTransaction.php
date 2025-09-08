<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\Transaction;
use App\Models\Wallet;
use Livewire\Component;
use Illuminate\Support\Facades\Request;

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


    // total_value price update function

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

    // select data

    public $wallets = [];
    public $assets = [];

    public function mount()
    {

        $this->wallets = Wallet::where('user_id', Auth::id())
            ->pluck('name', 'id')
            ->toArray();

        $this->assets = Asset::with('exchange')->get()->mapWithKeys(function ($asset) {
            return [$asset->id => $asset->name . ' / ' . $asset->exchange->name];
        })->toArray();
    }
    public $types = [
        'buy' => 'Buy',
        'sell' => 'Sell',
    ];


    public function save()

    {
        $this->validate([
            'wallet' => 'required|exists:wallets,id',
            'asset' => 'required|exists:assets,id', 
            'type' => 'required|string', 'currency' => 
            'required|string|max:3', 
            'quantity' => 'required|numeric', 
            'price_per_unit' => 'required|numeric', 
            'total_fees' => 'required|numeric', 
            'total_value' => 'required|numeric', 
            'date' => 'required|date', 
            'notes' => 'nullable|string|max:500'
        ]);

        Transaction::create([
            'wallet_id' => $this->wallet,
            'asset_id'  => $this->asset,
            'type' => $this->type,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'price_per_unit' => $this->price_per_unit,
            'total_fees' => $this->total_fees,
            'total_value' => $this->total_value,
            'date' => $this->date,
            'notes' => $this->notes,
        ]);

     // Tutaj flash i redirect
    session()->flash('success', 'Transaction saved!');
    return redirect()->route('my-transactions');
    }
    public function render()
    {
        return view('livewire.new-transaction', [
            'wallets' => $this->wallets,
            'assets' => $this->assets,
        ]);
    }
}
