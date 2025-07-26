<div>
    <div class="p-4">
        <div class="flex flex-row justify-between items-center mb-4 gap-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Szukaj po opisie..."
                class="mb-4 px-4 py-2 border rounded h-14 w-94 shrink" />
            <div class="flex gap-2">
                <button class="px-4 py-2 border rounded h-14 w-36 hover:bg-gray-600 transition">
                    Bulk Actions
                </button>
                <button class="px-4 py-2 border rounded h-14 w-36 hover:bg-gray-600 transition">
                    Columns
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <x-table>
                <x-slot name="head">
                    <x-table.header><input type="checkbox"></x-table.header>
                    <x-table.header sortable wire:click="sortBy('id')">ID</x-table.header>
                    <x-table.header sortable wire:click="sortBy('asset')" class="px-4 py-2">Asset</x-table.header>
                    <x-table.header sortable wire:click="sortBy('exchange')" class="px-4 py-2">Exchange</x-table.header>
                    <x-table.header sortable wire:click="sortBy('wallet')" class="px-4 py-2">Wallet</x-table.header>
                    <x-table.header sortable wire:click="sortBy('type')" class="px-4 py-2">Type</x-table.header>
                    <x-table.header sortable wire:click="sortBy('total_value')" class="px-4 py-2">Total Value</x-table.header>
                    <x-table.header sortable wire:click="sortBy('date')" class="px-4 py-2">Date</x-table.header>

                </x-slot>
                <x-slot name="body">
                    @forelse ($transactions as $transaction)
                        <x-table.row wire.loading.class.delay="opacity-50">  
                            <x-table.cell class="px-4 py-2">
                                <input type="checkbox">
                            </x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->id }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->asset?->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->asset?->exchange?->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ ucfirst($transaction->type) }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->total_value }}<span> {{  $transaction->currency}}</span></x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->date->format('M, d Y') }}</x-table.cell>

                        </x-table.row>
                    @empty
                        <tr><td colspan="7" class="text-center p-4">Brak wyników</td></tr>
                    @endforelse
                </x-slot>
            </x-table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
