<div class="mx-auto w-full max-w-[1600px] space-y-6 px-4 sm:px-6 lg:px-8">
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-5">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600 dark:text-blue-400">Activity</p>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    {{ $contextTitle ? 'Transactions for '.$contextTitle : 'Transactions' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-zinc-400">
                    {{ $contextTitle ? 'All recorded transactions in this context.' : 'All recorded transactions across your wallets.' }}
                </p>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            @if (session()->has('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">{{ session('success') }}</div>
            @endif

            @if (session()->has('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end">
                <button type="button" wire:click="resetFilters"
                    class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-rose-700 transition hover:border-rose-300 hover:bg-rose-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-rose-300 dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10">
                    Reset filters
                </button>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label for="transaction-search" class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Search</label>
                    <input id="transaction-search" type="search" wire:model.live.debounce.500ms="search" placeholder="Find by asset, wallet or broker"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500" />
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label for="transaction-date-from" class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Date from</label>
                    <input id="transaction-date-from" type="date" wire:model.live="dateFrom" max="{{ now()->toDateString() }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500" />
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <label for="transaction-date-to" class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Date to</label>
                    <input id="transaction-date-to" type="date" wire:model.live="dateTo" max="{{ now()->toDateString() }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 transition hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800 dark:focus:ring-zinc-500" />
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="mb-4 flex justify-end">
            <button type="button" wire:click="$dispatch('openNewTransactionModal')"
                class="inline-flex h-11 items-center justify-center rounded-lg bg-gray-900 px-5 text-sm font-semibold text-white transition hover:bg-gray-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300">
                New Transaction
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 text-xs uppercase tracking-[0.12em] text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                    <tr>
                        @foreach ([
                            'asset' => 'Asset', 'broker' => 'Broker', 'wallet' => 'Wallet', 'type' => 'Type',
                            'quantity' => 'Quantity', 'price' => 'Price', 'total_value' => 'Total value', 'date' => 'Date',
                        ] as $field => $label)
                            <th scope="col" class="whitespace-nowrap px-2 py-3 font-semibold">
                                <button type="button" wire:click="sortBy('{{ $field }}')" class="inline-flex items-center gap-1 transition hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $label }}
                                    @if ($sortField === $field)
                                        <span aria-hidden="true">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                        @endforeach
                        <th scope="col" class="whitespace-nowrap px-2 py-3 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse ($transactions as $transaction)
                        <tr wire:loading.class.delay="opacity-50">
                            <td class="whitespace-nowrap px-2 py-3 text-gray-700 dark:text-zinc-200">{{ $transaction->asset?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-2 py-3 text-gray-700 dark:text-zinc-200">{{ $transaction->wallet?->broker?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-2 py-3 text-gray-700 dark:text-zinc-200">{{ $transaction->wallet?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-2 py-3">
                                <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">{{ strtoupper($transaction->type) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-2 py-3 tabular-nums text-gray-700 dark:text-zinc-200">{{ abs($transaction->quantity) }}</td>
                            <td class="whitespace-nowrap px-2 py-3 tabular-nums text-gray-700 dark:text-zinc-200">
                                {{ number_format($transaction->price_per_unit, 2, ',', ' ') }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->asset?->exchange?->currency ?? $transaction->currency }}</span>
                            </td>
                            <td class="whitespace-nowrap px-2 py-3 tabular-nums text-gray-700 dark:text-zinc-200">
                                {{ number_format(abs($transaction->total_value), 2, ',', ' ') }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                            </td>
                            <td class="whitespace-nowrap px-2 py-3 text-gray-700 dark:text-zinc-200">{{ $transaction->date->format('Y-m-d') }}</td>
                            <td class="whitespace-nowrap px-2 py-3 text-center">
                                <div class="flex justify-center gap-1">
                                    <button type="button" wire:click="$dispatchTo('transaction.form-modal', 'openTransactionFormModal', { transactionId: {{ $transaction->id }} })" aria-label="Edit transaction" class="rounded p-1 text-emerald-600 transition-colors hover:bg-emerald-100 dark:text-emerald-400 dark:hover:bg-emerald-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M216-144q-29.7 0-50.85-21.15Q144-186.3 144-216v-528q0-30.11 21-51.56Q186-817 216-816h346l-72 72H216v528h528v-274l72-72v346q0 29.7-21.15 50.85Q773.7-144 744-144H216Zm264-336Zm-96 96v-153l354-354q11-11 24-16t26.5-5q14.4 0 27.45 5 13.05 5 23.99 15.78L891-840q11 11 16 24t5 27q0 14-5.02 27.09Q901.96-748.83 891-738L537-384H384Zm456-405-51-51 51 51ZM456-456h51l231-231-25-26-26-25-231 231v51Zm257-257-26-25 26 25 25 26-25-26Z" /></svg>
                                    </button>
                                    <button type="button" wire:click="deleteTransaction({{ $transaction->id }})" wire:confirm="Delete this transaction?" aria-label="Delete transaction" class="rounded p-1 text-rose-600 transition-colors hover:bg-rose-100 dark:text-rose-400 dark:hover:bg-rose-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 -960 960 960" width="22" fill="currentColor"><path d="m338-288-50-50 141-142-141-141 50-50 142 141 141-141 50 50-141 141 141 142-50 50-141-141-142 141Z" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-2 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 flex flex-col gap-4 border-t border-gray-200 pt-4 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-zinc-300">
                <span>Per page</span>
                <select wire:model.live="perPage" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:outline-hidden focus:ring-2 focus:ring-slate-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </label>

            @if ($transactions->hasPages())
                {{ $transactions->links('pagination.tailwind') }}
            @endif
        </div>
    </section>

    <livewire:transaction.form-modal />
</div>
