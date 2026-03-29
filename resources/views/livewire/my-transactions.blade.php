<div class="mx-auto w-full max-w-[1600px] space-y-6 px-4 py-4 sm:px-6 lg:px-8">
    <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">Transactions</span>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Your transaction history</h1>
                    <p class="mt-2 max-w-3xl text-sm text-gray-500 dark:text-zinc-400">Unified filters, table spacing and controls consistent with the rest of the app.</p>
                </div>

                <button wire:click="$dispatch('openNewTransactionModal')" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-500">
                    New Transaction
                </button>
            </div>

            @if (session()->has('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">{{ session('success') }}</div>
            @endif

            @if (session()->has('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_auto]">
                <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_180px]">
                    <div class="space-y-2">
                        <label class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Search</label>
                        <input type="text" wire:model.live.debounce.500ms="search" placeholder="Find by asset, wallet or broker" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Date From</label>
                        <input type="date" wire:model.live="dateFrom" max="{{ now()->toDateString() }}" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Date To</label>
                        <input type="date" wire:model.live="dateTo" max="{{ now()->toDateString() }}" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                    </div>
                </div>

                <div class="space-y-2 xl:w-32">
                    <label class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Per Page</label>
                    <select wire:model.live="perPage" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 outline-none transition hover:bg-gray-50 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white dark:hover:bg-zinc-900">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-zinc-800">
                <x-table>
                    <x-slot name="head">
                        <x-table.header sortable wire:click="sortBy('asset')" :direction="$sortField === 'asset' ? $sortDirection : null" class="px-4 py-3">Asset</x-table.header>
                        <x-table.header sortable wire:click="sortBy('broker')" :direction="$sortField === 'broker' ? $sortDirection : null" class="px-4 py-3">Broker</x-table.header>
                        <x-table.header sortable wire:click="sortBy('wallet')" :direction="$sortField === 'wallet' ? $sortDirection : null" class="px-4 py-3">Wallet</x-table.header>
                        <x-table.header sortable wire:click="sortBy('type')" :direction="$sortField === 'type' ? $sortDirection : null" class="px-4 py-3">Type</x-table.header>
                        <x-table.header sortable wire:click="sortBy('quantity')" :direction="$sortField === 'quantity' ? $sortDirection : null" class="px-4 py-3 text-right">Quantity</x-table.header>
                        <x-table.header sortable wire:click="sortBy('price')" :direction="$sortField === 'price' ? $sortDirection : null" class="px-4 py-3 text-right">Price</x-table.header>
                        <x-table.header sortable wire:click="sortBy('total_value')" :direction="$sortField === 'total_value' ? $sortDirection : null" class="px-4 py-3 text-right">Total Value</x-table.header>
                        <x-table.header sortable wire:click="sortBy('date')" :direction="$sortField === 'date' ? $sortDirection : null" class="px-4 py-3">Date</x-table.header>
                        <x-table.header class="w-32 !text-center">Actions</x-table.header>
                    </x-slot>

                    <x-slot name="body">
                        @forelse ($transactions as $transaction)
                            <x-table.row wire.loading.class.delay="opacity-50">
                                <x-table.cell class="px-4 py-3">{{ $transaction->asset?->name }}</x-table.cell>
                                <x-table.cell class="px-4 py-3">{{ $transaction->wallet->broker->name }}</x-table.cell>
                                <x-table.cell class="px-4 py-3">{{ $transaction->wallet?->name }}</x-table.cell>
                                <x-table.cell class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">{{ ucfirst($transaction->type) }}</span></x-table.cell>
                                <x-table.cell class="px-4 py-3 text-right">{{ number_format($transaction->quantity, 4, ',', ' ') }}</x-table.cell>
                                <x-table.cell class="px-4 py-3 text-right">{{ number_format($transaction->price_per_unit, 2, ',', ' ') }} <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->asset->exchange->currency ?? $transaction->currency }}</span></x-table.cell>
                                <x-table.cell class="px-4 py-3 text-right">{{ number_format(abs($transaction->total_value), 2, ',', ' ') }} <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span></x-table.cell>
                                <x-table.cell class="px-4 py-3">{{ $transaction->date->format('M d, Y') }}</x-table.cell>
                                <x-table.cell class="w-32 text-center">
                                    <div class="flex justify-center space-x-1">
                                        <button wire:click="$dispatchTo('transaction-form-modal', 'openTransactionFormModal', { transactionId: {{ $transaction->id }} })" class="rounded-lg p-2 transition-colors hover:bg-green-100 dark:hover:bg-green-900/30">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#22c55e"><path d="M216-144q-29.7 0-50.85-21.15Q144-186.3 144-216v-528q0-30.11 21-51.56Q186-817 216-816h346l-72 72H216v528h528v-274l72-72v346q0 29.7-21.15 50.85Q773.7-144 744-144H216Zm264-336Zm-96 96v-153l354-354q11-11 24-16t26.5-5q14.4 0 27.45 5 13.05 5 23.99 15.78L891-840q11 11 16 24t5 27q0 14-5.02 27.09Q901.96-748.83 891-738L537-384H384Zm456-405-51-51 51 51ZM456-456h51l231-231-25-26-26-25-231 231v51Zm257-257-26-25 26 25 25 26-25-26Z" /></svg>
                                        </button>
                                        <button wire:click="deleteTransaction({{ $transaction->id }})" wire:confirm="Delete this transaction?" class="rounded-lg p-1 transition-colors hover:bg-red-100 dark:hover:bg-red-900/30">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#dc2626"><path d="m338-288-50-50 141-142-141-141 50-50 142 141 141-141 50 50-141 141 141 142-50 50-141-141-142 141Z" /></svg>
                                        </button>
                                    </div>
                                </x-table.cell>
                            </x-table.row>
                        @empty
                            <x-table.row>
                                <x-table.cell colspan="9">
                                    <div class="flex items-center justify-center py-10"><span class="text-base font-medium text-gray-500 dark:text-zinc-400">No transactions found.</span></div>
                                </x-table.cell>
                            </x-table.row>
                        @endforelse
                    </x-slot>
                </x-table>
            </div>

            <div>{{ $transactions->links() }}</div>
        </div>
    </section>

    <livewire:transaction-form-modal />
</div>
