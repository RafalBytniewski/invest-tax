<div class="mx-auto max-w-[1600px] space-y-6 p-4 sm:p-6">
    <section class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 sm:p-6">
        <div class="space-y-6 p-4">
            @if (session()->has('success'))
                <div class="mb-3 rounded bg-green-100 p-2 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-3 rounded bg-red-100 p-2 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-3 rounded bg-red-100 p-2 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-5">
                <div class="space-y-2">
                    <p class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Transactions
                    </p>

                    <p class="max-w-2xl text-sm text-gray-500 dark:text-zinc-400">
                        Search, filter and manage all transactions assigned to your wallets.
                    </p>
                </div>

                <div class="border-t border-gray-200 dark:border-zinc-800"></div>

                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_minmax(0,0.7fr)_auto] xl:items-end">
                    <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <label class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                            Search
                        </label>
                        <input type="text" wire:model.live.debounce.500ms="search"
                            placeholder="Find transaction, asset or wallet..."
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500" />
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                            Date range
                        </p>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-medium text-gray-500 dark:text-zinc-400">Date from</label>
                                <input type="date" wire:model.live="dateFrom" max="{{ now()->toDateString() }}"
                                    class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500">
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-medium text-gray-500 dark:text-zinc-400">Date to</label>
                                <input type="date" wire:model.live="dateTo" max="{{ now()->toDateString() }}"
                                    class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <label class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                            Per page
                        </label>
                        <select wire:model.live="perPage"
                            class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <div class="flex h-full flex-col justify-end gap-3">
                        <button wire:click="$dispatch('openNewTransactionModal')"
                            class="inline-flex h-12 items-center justify-center rounded-xl border border-red-800 bg-red-800 px-6 text-sm font-semibold text-white transition hover:border-red-700 hover:bg-red-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-rose-300 dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10">
                            New Transaction
                        </button>

                        <button wire:click="resetFilters"
                            class="inline-flex h-12 items-center justify-center rounded-xl border border-gray-300 bg-white px-5 text-sm font-semibold text-gray-700 transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10 dark:hover:text-rose-300">
                            Reset filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 sm:p-6">
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

                    <x-table.header class="w-32 !text-center">Actions</x-table.header>
                </x-slot>

                <x-slot name="body">
                    @forelse ($transactions as $transaction)
                        <x-table.row wire.loading.class.delay="opacity-50">
                            <x-table.cell class="px-4 py-2">{{ $transaction->asset?->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->wallet->broker->name }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->wallet?->name }}</x-table.cell>
                            <x-table.cell
                                class="px-4 py-2 {{ $transaction->type === 'sell' ? 'text-red-500' : 'text-green-500' }}">
                                {{ ucfirst($transaction->type) }}
                            </x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->quantity }}</x-table.cell>
                            <x-table.cell class="px-4 py-2">
                                {{ number_format($transaction->price_per_unit, 2, ',', ' ') }}
                                <span>{{ $transaction->asset->exchange->currency ?? $transaction->currency }}</span>
                            </x-table.cell>
                            <x-table.cell class="px-4 py-2">
                                {{ number_format(abs($transaction->total_value), 2, ',', ' ') }}
                                <span>{{ $transaction->currency }}</span>
                            </x-table.cell>
                            <x-table.cell class="px-4 py-2">{{ $transaction->date->format('M, d Y') }}</x-table.cell>
                            <x-table.cell class="w-32 text-center">
                                <div class="flex justify-center space-x-1">
                                    <button
                                        wire:click="$dispatchTo('transaction-form-modal', 'openTransactionFormModal', { transactionId: {{ $transaction->id }} })"
                                        class="rounded p-1 transition-colors hover:bg-green-100 dark:hover:bg-green-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                                            width="20px" fill="#75FB4C">
                                            <path
                                                d="M216-144q-29.7 0-50.85-21.15Q144-186.3 144-216v-528q0-30.11 21-51.56Q186-817 216-816h346l-72 72H216v528h528v-274l72-72v346q0 29.7-21.15 50.85Q773.7-144 744-144H216Zm264-336Zm-96 96v-153l354-354q11-11 24-16t26.5-5q14.4 0 27.45 5 13.05 5 23.99 15.78L891-840q11 11 16 24t5 27q0 14-5.02 27.09Q901.96-748.83 891-738L537-384H384Zm456-405-51-51 51 51ZM456-456h51l231-231-25-26-26-25-231 231v51Zm257-257-26-25 26 25 25 26-25-26Z" />
                                        </svg>
                                    </button>

                                    <button wire:click="deleteTransaction({{ $transaction->id }})"
                                        wire:confirm="Delete this transaction?"
                                        class="rounded transition-colors hover:bg-red-100 dark:hover:bg-red-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960"
                                            width="30px" fill="#8B1A10">
                                            <path
                                                d="m338-288-50-50 141-142-141-141 50-50 142 141 141-141 50 50-141 141 141 142-50 50-141-141-142 141Z" />
                                        </svg>
                                    </button>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="9">
                                <div class="flex items-center justify-center">
                                    <span class="py-8 text-2xl font-medium text-gray-500">
                                        No transactions found...
                                    </span>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </section>

    <div>
        <livewire:transaction-form-modal />
    </div>
</div>
