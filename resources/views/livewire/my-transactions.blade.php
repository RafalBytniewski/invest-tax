<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-6">


            <div class="space-y-2">
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Transactions
                </h1>

            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            @if (session()->has('success'))
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div
                    class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-end">
                <button type="button" wire:click="resetFilters"
                    class="inline-flex h-12 w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-5 text-sm font-semibold transition hover:border-rose-300 hover:bg-rose-50 text-rose-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10 dark:text-rose-300 xl:w-auto">
                    Reset filters
                </button>
            </div>

            <div
                class="grid gap-4 lg:grid-cols-2 xl:grid-cols-[minmax(0,1.4fr)_repeat(2,minmax(0,1fr))_180px] xl:items-stretch">
                <div
                    class="flex h-full flex-col rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label
                        class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                        Search
                    </label>
                    <input type="text" wire:model.live.debounce.500ms="search"
                        placeholder="Find by asset, wallet or broker"
                        class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500" />
                </div>

                <div
                    class="flex h-full flex-col rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label
                        class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                        Date from
                    </label>
                    <input type="date" wire:model.live="dateFrom" max="{{ now()->toDateString() }}"
                        class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500">
                </div>

                <div
                    class="flex h-full flex-col rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label
                        class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                        Date to
                    </label>
                    <input type="date" wire:model.live="dateTo" max="{{ now()->toDateString() }}"
                        class="h-12 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500">
                </div>

                <div
                    class="flex h-full flex-col rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label
                        class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
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
            </div>

    </section>
    <section class="mx-auto rounded-xl border bg-white p-2 dark:border-zinc-800 dark:bg-zinc-900 sm:px-3">
        <div class="flex justify-end">
            <button wire:click="$dispatch('openNewTransactionModal')"
                class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-900 bg-slate-900 px-5 my-2 text-l font-semibold text-white transition hover:bg-slate-800 dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 cursor-pointer">
                New Transaction
            </button>
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

                            <x-table.cell class="px-4 py-2">{{ $transaction->date->format('M, d Y') }}</x-table.cell>

                            <x-table.cell class="w-32 text-center">
                                <div class="flex space-x-1 justify-center">

                                    <!-- Edit -->
                                    <button
                                        wire:click="$dispatchTo('transaction-form-modal', 'openTransactionFormModal', { transactionId: {{ $transaction->id }} })"
                                        class="p-1 rounded hover:bg-green-100 dark:hover:bg-green-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                                            width="20px" fill="#75FB4C">
                                            <path
                                                d="M216-144q-29.7 0-50.85-21.15Q144-186.3 144-216v-528q0-30.11 21-51.56Q186-817 216-816h346l-72 72H216v528h528v-274l72-72v346q0 29.7-21.15 50.85Q773.7-144 744-144H216Zm264-336Zm-96 96v-153l354-354q11-11 24-16t26.5-5q14.4 0 27.45 5 13.05 5 23.99 15.78L891-840q11 11 16 24t5 27q0 14-5.02 27.09Q901.96-748.83 891-738L537-384H384Zm456-405-51-51 51 51ZM456-456h51l231-231-25-26-26-25-231 231v51Zm257-257-26-25 26 25 25 26-25-26Z" />
                                        </svg>
                                    </button>

                                    <!-- Delete -->
                                    <button wire:click="deleteTransaction({{ $transaction->id }})"
                                        wire:confirm="Delete this transaction?"
                                        class=" rounded hover:bg-red-100 dark:hover:bg-red-800 transition-colors">
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
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
</div>
</section>
<div>
    <livewire:transaction-form-modal />
</div>
</div>
