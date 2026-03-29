<?php

namespace App\Livewire;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Assets extends Component
{
    public ?string $type = null;

    protected function query(): Builder
    {
        return Asset::query()
            ->with('exchange')
            ->orderBy('name')
            ->when($this->type, fn (Builder $query) => $query->where('asset_type', $this->type));
    }

    public function render(): View
    {
        return view('livewire.assets', [
            'assets' => $this->query()->get(),
        ]);
    }
}
