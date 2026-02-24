<div class="space-y-4">
    <div class="p-4 space-y-4">
        @if (session()->has('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-xl border border-gray-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="grid gap-3 lg:grid-cols-[1fr_auto] lg:items-center">
                <div class="space-y-3">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search by asset, wallet, or broker"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                    />

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="setType('')"
                            class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $type === '' ? 'border-blue-600 bg-blue-600 text-white dark:border-blue-500 dark:bg-blue-500' : 'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700' }}"
                        >
                            All
                        </button>
                        <button
                            type="button"
                            wire:click="setType('buy')"
                            class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $type === 'buy' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700' }}"
                        >
                            Buy
                        </button>
                        <button
                            type="button"
                            wire:click="setType('sell')"
                            class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $type === 'sell' ? 'border-rose-600 bg-rose-600 text-white' : 'border-gray-300 bg-gray-50 text-gray-700 hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700' }}"
                        >
                            Sell
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 lg:justify-end">
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            class="h-11 rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                        >
                            Columns
                        </button>
                        <div
                            x-show="open"
                            x-cloak
                            @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-zinc-700 dark:bg-zinc-900 z-20"
                        >
                            @foreach ($columnVisibility as $column => $isVisible)
                                <label class="flex items-center gap-2 rounded px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                    <input type="checkbox" wire:model.live="columnVisibility.{{ $column }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>{{ ucwords(str_replace('_', ' ', $column)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="h-11 rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                    >
                        Reset
                    </button>

                    <button
                        type="button"
                        wire:click="$dispatchTo('new-transaction', 'openModal')"
                        class="h-11 rounded-lg border border-red-700 bg-red-700 px-3 text-sm font-semibold text-white transition hover:bg-red-600"
                    >
                        New Transaction
                    </button>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500 dark:text-zinc-400">
                <span>{{ $transactions->total() }} {{ \Illuminate\Support\Str::plural('transaction', $transactions->total()) }}</span>
                <div class="flex items-center gap-2">
                    <label for="per-page" class="font-medium">Per page:</label>
                    <select
                        id="per-page"
                        wire:model.live="perPage"
                        class="h-9 rounded-lg border border-gray-300 bg-gray-50 px-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                    >
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="relative overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div wire:loading.flex wire:target="search,type,sortBy,perPage" class="absolute inset-0 z-10 items-center justify-center bg-white/70 text-sm font-semibold text-gray-700 dark:bg-zinc-900/70 dark:text-zinc-200">
                Loading transactions...
            </div>

            <x-table>
                <x-slot name="head">
                    <x-table.header>
                        <input type="checkbox" wire:model.live="selectAll" aria-label="Select all transactions">
                    </x-table.header>

                    @if ($columnVisibility['asset'])
                        <x-table.header sortable wire:click="sortBy('asset')" :direction="$sortField === 'asset' ? $sortDirection : null" class="px-4 py-2">Asset</x-table.header>
                    @endif
                    @if ($columnVisibility['broker'])
                        <x-table.header sortable wire:click="sortBy('broker')" :direction="$sortField === 'broker' ? $sortDirection : null" class="px-4 py-2">Broker</x-table.header>
                    @endif
                    @if ($columnVisibility['wallet'])
                        <x-table.header sortable wire:click="sortBy('wallet')" :direction="$sortField === 'wallet' ? $sortDirection : null" class="px-4 py-2">Wallet</x-table.header>
                    @endif
                    @if ($columnVisibility['type'])
                        <x-table.header sortable wire:click="sortBy('type')" :direction="$sortField === 'type' ? $sortDirection : null" class="px-4 py-2">Type</x-table.header>
                    @endif
                    @if ($columnVisibility['quantity'])
                        <x-table.header sortable wire:click="sortBy('quantity')" :direction="$sortField === 'quantity' ? $sortDirection : null" class="px-4 py-2 text-right">Quantity</x-table.header>
                    @endif
                    @if ($columnVisibility['price'])
                        <x-table.header class="px-4 py-2 text-right">Price</x-table.header>
                    @endif
                    @if ($columnVisibility['total_value'])
                        <x-table.header sortable wire:click="sortBy('total_value')" :direction="$sortField === 'total_value' ? $sortDirection : null" class="px-4 py-2 text-right">Total Value</x-table.header>
                    @endif
                    @if ($columnVisibility['date'])
                        <x-table.header sortable wire:click="sortBy('date')" :direction="$sortField === 'date' ? $sortDirection : null" class="px-4 py-2">Date</x-table.header>
                    @endif

                    <x-table.header>Actions</x-table.header>
                </x-slot>

                <x-slot name="body">
                    @forelse ($transactions as $transaction)
                        <x-table.row wire:key="transaction-row-{{ $transaction->id }}" wire:loading.class.delay="opacity-60">
                            <x-table.cell class="px-4 py-2">
                                <input type="checkbox" value="{{ $transaction->id }}" wire:model.live="selected" aria-label="Select transaction {{ $transaction->id }}">
                            </x-table.cell>

                            @if ($columnVisibility['asset'])
                                <x-table.cell class="px-4 py-2">{{ $transaction->asset?->name ?? 'Unknown asset' }}</x-table.cell>
                            @endif
                            @if ($columnVisibility['broker'])
                                <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->broker?->name ?? 'Unknown broker' }}</x-table.cell>
                            @endif
                            @if ($columnVisibility['wallet'])
                                <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->name ?? 'Unknown wallet' }}</x-table.cell>
                            @endif
                            @if ($columnVisibility['type'])
                                <x-table.cell class="px-4 py-2 {{ $transaction->type === 'sell' ? 'text-rose-500' : 'text-emerald-500' }}">{{ ucfirst($transaction->type) }}</x-table.cell>
                            @endif
                            @if ($columnVisibility['quantity'])
                                <x-table.cell class="px-4 py-2 text-right">{{ number_format(abs($transaction->quantity), 8, '.', ' ') }}</x-table.cell>
                            @endif
                            @if ($columnVisibility['price'])
                                <x-table.cell class="px-4 py-2 text-right">
                                    {{ number_format($transaction->price_per_unit, 2, '.', ' ') }}
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->asset?->exchange?->currency ?? $transaction->currency }}</span>
                                </x-table.cell>
                            @endif
                            @if ($columnVisibility['total_value'])
                                <x-table.cell class="px-4 py-2 text-right">
                                    {{ number_format(abs($transaction->total_value), 2, '.', ' ') }}
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                                </x-table.cell>
                            @endif
                            @if ($columnVisibility['date'])
                                <x-table.cell class="px-4 py-2">{{ $transaction->date->format('M d, Y') }}</x-table.cell>
                            @endif

                            <x-table.cell class="px-4 py-2">
                                <div class="flex flex-wrap gap-1">
                                    <button type="button" class="rounded border border-blue-700 bg-blue-700 px-2 py-1 text-xs font-semibold text-white hover:bg-blue-600" aria-label="View transaction {{ $transaction->id }}" title="View">View</button>
                                    <button type="button" class="rounded border border-emerald-700 bg-emerald-700 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-600" aria-label="Edit transaction {{ $transaction->id }}" title="Edit">Edit</button>
                                    <button type="button" class="rounded border border-rose-700 bg-rose-700 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-600" aria-label="Delete transaction {{ $transaction->id }}" title="Delete">Delete</button>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="{{ $this->visibleColumnCount() + 2 }}">
                                <div class="flex flex-col items-center justify-center py-10 text-center">
                                    <span class="text-base font-medium text-gray-600 dark:text-zinc-300">No transactions found.</span>
                                    @if ($search !== '' || $type !== '')
                                        <button type="button" wire:click="resetFilters" class="mt-3 rounded-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                            Clear filters
                                        </button>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>
        </section>

        <div>
            {{ $transactions->links() }}
        </div>
    </div>

    <div>
        <livewire:new-transaction wire:key="new-transaction-modal" />
    </div>
</div>
