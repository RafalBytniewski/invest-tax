<div>
    @php
        $visibleColumnCount = collect($columns)->filter()->count();
    @endphp

    <div class="p-4">
        @if (session()->has('success'))
            <div class="p-2 mb-3 text-green-700 bg-green-100 rounded">
                {{ session('success') }}
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
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Find ..."
                    class="px-4 py-2 border rounded h-11 w-full sm:w-72 lg:w-96"
                />
                <div class="flex flex-col">
                    <label class="text-xs mb-1 text-gray-600 dark:text-gray-300">Date from</label>
                    <input type="date" wire:model.live="dateFrom" class="px-3 py-2 border rounded h-11 w-full sm:w-44">
                </div>
                <div class="flex flex-col">
                    <label class="text-xs mb-1 text-gray-600 dark:text-gray-300">Date to</label>
                    <input type="date" wire:model.live="dateTo" class="px-3 py-2 border rounded h-11 w-full sm:w-44">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <button
                    wire:click="deleteSelected"
                    wire:confirm="Delete selected transactions?"
                    @disabled($this->selectedCount === 0)
                    class="px-4 py-2 border rounded h-11 w-full sm:w-44 transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-red-600 hover:text-white"
                >
                    Delete Selected
                </button>

                <div x-data="{ open: false }" class="relative inline-block text-left">
                    <button @click="open = !open" class="px-4 py-2 border rounded h-11 w-full sm:w-36 hover:bg-gray-600 transition">
                        Columns
                    </button>
                    <div
                        x-show="open"
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-44 bg-white dark:bg-gray-600 dark:border-gray-700 border rounded z-50"
                    >
                        @foreach ($columnOptions as $key => $config)
                            <label class="flex items-center space-x-2 px-2 py-1 bg-white dark:bg-gray-700 dark:hover:bg-gray-600">
                                <input
                                    type="checkbox"
                                    wire:model.live="columns.{{ $key }}"
                                    @disabled($config['required'])
                                    class="text-blue-600 focus:ring-blue-500"
                                >
                                <span class="text-gray-900 dark:text-white">{{ $config['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button
                    wire:click="$dispatch('openNewTransactionModal')"
                    class="px-4 py-2 border bg-red-800 rounded h-11 w-full sm:w-44 hover:bg-red-400 transition"
                >
                    New Transaction
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <x-table>
                <x-slot name="head">
                    <x-table.header class="!text-center">
                        <input type="checkbox" wire:model.live="selectAll">
                    </x-table.header>

                    @foreach ($columnOptions as $key => $config)
                        @if ($columns[$key] ?? false)
                            <x-table.header
                                sortable
                                wire:click="sortBy('{{ $key }}')"
                                :direction="$sortField === $key ? $sortDirection : null"
                                class="px-4 py-2"
                            >
                                {{ $config['label'] }}
                            </x-table.header>
                        @endif
                    @endforeach

                    <x-table.header class="!text-center w-24">Actions</x-table.header>
                </x-slot>

                <x-slot name="body">
                    @forelse ($transactions as $transaction)
                        <x-table.row wire:loading.class.delay="opacity-50">
                            <x-table.cell class="px-4 py-2 text-center">
                                <input type="checkbox" wire:model.live="selected.{{ $transaction->id }}">
                            </x-table.cell>

                            @foreach ($columnOptions as $key => $config)
                                @if ($columns[$key] ?? false)
                                    @switch($key)
                                        @case('asset')
                                            <x-table.cell class="px-4 py-2">{{ $transaction->asset?->name }}</x-table.cell>
                                            @break

                                        @case('broker')
                                            <x-table.cell class="px-4 py-2">{{ $transaction->wallet->broker->name }}</x-table.cell>
                                            @break

                                        @case('wallet')
                                            <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->name }}</x-table.cell>
                                            @break

                                        @case('type')
                                            <x-table.cell class="px-4 py-2 {{ $transaction->type === 'sell' ? 'text-red-500' : 'text-green-500' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </x-table.cell>
                                            @break

                                        @case('quantity')
                                            <x-table.cell class="px-4 py-2">{{ abs($transaction->quantity) }}</x-table.cell>
                                            @break

                                        @case('price')
                                            <x-table.cell class="px-4 py-2">
                                                {{ number_format($transaction->price_per_unit, 2, ',', ' ') }}
                                                <span>{{ $transaction->asset->exchange->currency ?? $transaction->currency }}</span>
                                            </x-table.cell>
                                            @break

                                        @case('total_value')
                                            <x-table.cell class="px-4 py-2">
                                                {{ number_format(abs($transaction->total_value), 2, ',', ' ') }}
                                                <span>{{ $transaction->currency }}</span>
                                            </x-table.cell>
                                            @break

                                        @case('date')
                                            <x-table.cell class="px-4 py-2">{{ $transaction->date->format('M, d Y') }}</x-table.cell>
                                            @break
                                    @endswitch
                                @endif
                            @endforeach

                            <x-table.cell class="w-24 text-center">
                                <button
                                    wire:click="deleteTransaction({{ $transaction->id }})"
                                    wire:confirm="Delete this transaction?"
                                    class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-800 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="{{ $visibleColumnCount + 2 }}">
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

        <div class="mt-3 flex items-center gap-2">
            <label class="py-2">Per page:</label>
            <select wire:model.live="perPage" class="px-4 py-2 border rounded h-11 w-24 hover:bg-gray-600 transition">
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

    <div>
        <livewire:new-transaction />
    </div>
</div>
