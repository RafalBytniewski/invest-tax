<div>
    <div>
        <div class="p-4">

            <!-- Messages -->
            @if (session()->has('success'))
                <div class="p-2 mb-3 text-green-700 bg-green-100 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="p-2 mb-3 text-red-700 bg-red-100 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-2 mb-3 text-red-700 bg-red-100 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-end mb-4 gap-3">
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:items-end w-full lg:w-auto">
                    <input type="text" wire:model.live.debounce.500ms="search" placeholder="Find ..."
                        class="px-4 py-2 border rounded h-11 w-full sm:w-72 lg:w-96" />
                    <div class="flex flex-col">
                        <label class="text-xs mb-1 text-gray-600 dark:text-gray-300">Date from</label>
                        <input type="date" wire:model.live="dateFrom"
                            class="px-3 py-2 border rounded h-11 w-full sm:w-44">
                    </div>
                    <div class="flex flex-col">
                        <label class="text-xs mb-1 text-gray-600 dark:text-gray-300">Date to</label>
                        <input type="date" wire:model.live="dateTo"
                            class="px-3 py-2 border rounded h-11 w-full sm:w-44">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                    <!-- New Transaction -->
                    <button wire:click="$dispatch('openNewTransactionModal')"
                        class="px-4 py-2 border bg-red-800 rounded h-11 w-full sm:w-44 hover:bg-red-400 transition">
                        New Transaction
                    </button>
                </div>
            </div>

            <!-- Transactions tabel -->
            <div class="overflow-x-auto">
                <x-table>
                    <x-slot name="head">
                        <x-table.header sortable wire:click="sortBy('asset')" :direction="$sortField === 'asset' ? $sortDirection : null"
                            class="px-4 py-2">Asset</x-table.header>

                        <x-table.header sortable wire:click="sortBy('broker')" :direction="$sortField === 'broker' ? $sortDirection : null"
                            class="px-4 py-2">Broker</x-table.header>

                        <x-table.header sortable wire:click="sortBy('wallet')" :direction="$sortField === 'wallet' ? $sortDirection : null"
                            class="px-4 py-2">Wallet</x-table.header>

                        <x-table.header sortable wire:click="sortBy('type')" :direction="$sortField === 'type' ? $sortDirection : null"
                            class="px-4 py-2">Type</x-table.header>

                        <x-table.header sortable wire:click="sortBy('quantity')" :direction="$sortField === 'quantity' ? $sortDirection : null"
                            class="px-4 py-2">Quantity</x-table.header>

                        <x-table.header sortable wire:click="sortBy('price')" :direction="$sortField === 'price' ? $sortDirection : null"
                            class="px-4 py-2">Price</x-table.header>

                        <x-table.header sortable wire:click="sortBy('total_value')" :direction="$sortField === 'total_value' ? $sortDirection : null"
                            class="px-4 py-2">Total Value</x-table.header>

                        <x-table.header sortable wire:click="sortBy('date')" :direction="$sortField === 'date' ? $sortDirection : null"
                            class="px-4 py-2">Date</x-table.header>

                        <x-table.header class="!text-center w-32">Actions</x-table.header>
                    </x-slot>
                    <x-slot name="body">

                        @forelse ($transactions as $transaction)
                            <x-table.row wire.loading.class.delay="opacity-50">
                                <x-table.cell class="px-4 py-2">{{ $transaction->asset?->name }}</x-table.cell>

                                <x-table.cell class="px-4 py-2">{{ $transaction->wallet->broker->name }}</x-table.cell>

                                <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->name }}</x-table.cell>

                                <x-table.cell
                                    class="px-4 py-2  {{ $transaction->type === 'sell' ? 'text-red-500' : 'text-green-500' }}">{{ ucfirst($transaction->type) }}</x-table.cell>

                                <x-table.cell class="px-4 py-2">{{ $transaction->quantity }}</x-table.cell>

                                <x-table.cell
                                    class="px-4 py-2">{{ number_format($transaction->price_per_unit, 2, ',', ' ') }}<span>
                                        {{ $transaction->asset->exchange->currency ?? $transaction->currency }}</span></x-table.cell>

                                <x-table.cell
                                    class="px-4 py-2">{{ number_format(abs($transaction->total_value), 2, ',', ' ') }}<span>
                                        {{ $transaction->currency }}</span></x-table.cell>

                                <x-table.cell
                                    class="px-4 py-2">{{ $transaction->date->format('M, d Y') }}</x-table.cell>

                                <x-table.cell class="w-32 text-center">
                                    <div class="flex space-x-1 justify-center">
                                        <!-- V: Verify / Check -->
                                        <button
                                            class="p-1 rounded hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>

                                        <!-- E: Edit / Pencil -->
                                        <button
                                            class="p-1 rounded hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5h2m2 2l4 4M4 20h16" />
                                            </svg>
                                        </button>

                                        <!-- D: Delete / Trash -->
                                        <button wire:click="deleteTransaction({{ $transaction->id }})"
                                            wire:confirm="Delete this transaction?"
                                            class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-800 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </x-table.cell>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.cell colspan="9">
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

            <!-- Pagination -->
            <div class="mt-3 flex">
                <label for=""class="pr-2 py-2 ">Per page:</label>
                <select wire:model.live="perPage"
                    class="px-4 py-2 border rounded h-11 w-22 hover:bg-gray-600 transition">
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
        <livewire:new-transaction />
    </div>
</div>
