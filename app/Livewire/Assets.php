<?php

namespace App\Livewire;

use App\Models\Asset;
use Livewire\Component;

class Assets extends Component
{
    public $type = '';
    public $search = '';

    public function setType(?string $type = ''): void
    {
        $this->type = $type ?? '';
    }

    public function render()
    {
        $assets = Asset::query()
            ->with('exchange')
            ->orderBy('name')
            ->when($this->type, function ($query) {
                $query->where('asset_type', $this->type);
            })
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('symbol', 'like', "%{$search}%")
                        ->orWhereHas('exchange', function ($eq) use ($search) {
                            $eq->where('name', 'like', "%{$search}%")
                                ->orWhere('symbol', 'like', "%{$search}%");
                        });
                });
            })
            ->get();


        return view('livewire.assets', [
            'assets' => $assets
        ]);
    }
}
