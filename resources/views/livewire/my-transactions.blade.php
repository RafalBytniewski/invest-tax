
<div>
    <div>
        <div class="p-4">
            <div class="flex flex-row justify-between items-center mb-4 gap-4">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Find ..."
                    class="mb-4 px-4 py-2 border rounded h-14 w-94 shrink" />
                <div class="flex gap-2">
                    <button class="px-4 py-2 border rounded h-14 w-36 hover:bg-gray-600 transition">
                        Bulk Actions
                    </button>
                    <button class="px-4 py-2 border rounded h-14 w-36 hover:bg-gray-600 transition">
                        Columns
                    </button>
                    <button wire:click="$dispatch('openNewTransactionModal')"
                        class="px-4 py-2 border bg-red-800 rounded h-14 w-46 hover:bg-red-400 transition">
                        New Transaction
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <x-table>
                    <x-slot name="head">
                        <x-table.header><input type="checkbox"wire:model="selectAll"></x-table.header>
                        <x-table.header sortable wire:click="sortBy('asset')" :direction="$sortField === 'asset' ? $sortDirection : null"
                            class="px-4 py-2">Asset</x-table.header>
                        <x-table.header sortable wire:click="sortBy('exchange')" :direction="$sortField === 'exchange' ? $sortDirection : null"
                            class="px-4 py-2">Exchange</x-table.header>
                        <x-table.header sortable wire:click="sortBy('wallet')" :direction="$sortField === 'wallet' ? $sortDirection : null"
                            class="px-4 py-2">Wallet</x-table.header>
                        <x-table.header sortable wire:click="sortBy('type')" :direction="$sortField === 'type' ? $sortDirection : null"
                            class="px-4 py-2">Type</x-table.header>
                        <x-table.header sortable wire:click="sortBy('quantity')" :direction="$sortField === 'quantity' ? $sortDirection : null"
                            class="px-4 py-2">Quantity</x-table.header>
                        <x-table.header class="px-4 py-2">Price</x-table.header>
                        <x-table.header sortable wire:click="sortBy('total_value')" :direction="$sortField === 'total_value' ? $sortDirection : null"
                            class="px-4 py-2">Total Value</x-table.header>
                        <x-table.header sortable wire:click="sortBy('date')" :direction="$sortField === 'date' ? $sortDirection : null"
                            class="px-4 py-2">Date</x-table.header>
                        <x-table.header>Actions</x-table>
                </x-slot>
                <x-slot name="body">

                    @forelse ($transactions as $transaction)
                        <x-table.row wire.loading.class.delay="opacity-50">
                            <x-table.cell class="px-4 py-2">
                                <input type="checkbox" value="{{ $transaction->id }}" wire:model="selected">
                            </x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->asset?->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->asset?->exchange?->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->name }}</x-table.cell>
                            <x-table.cell
                                class="px-4 py-2  {{ $transaction->type === 'sell' ? 'text-red-500' : 'text-green-500' }}">{{ ucfirst($transaction->type) }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ abs($transaction->quantity) }}</x-table.cell>
                            <x-table.cell
                                class="px-4 py-2">{{ number_format($transaction->price_per_unit, 2, ',', ' ') }}<span>
                                    {{ $transaction->currency }}</span></x-table.cell>
                            <x-table.cell
                                class="px-4 py-2">{{ number_format(abs($transaction->total_value), 2, ',', ' ') }}<span>
                                    {{ $transaction->currency }}</span></x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->date->format('M, d Y') }}</x-table.cell>
                            <x-table.cell>
                                <button class="bg-blue-800 cursor-pointer m-1">V</button>
                                <button class="bg-green-800 cursor-pointer m-1">E</button>
                                <button class="bg-red-800 cursor-pointer m-1">D</button>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="8">
                                <div class="flex justify-center items-center">
                                    <span class="font-medium py-8 text-gray-500 text-2xl">
                                        No transactions found...
                                    </span>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
                </x-table>
            </div>
            <div class="mt-3 flex">
                <label for=""class="mr-5">Per page</label>
                <select wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>
            </div> 
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
    <div>
    <livewire:new-transaction/>
</div>
</div>

