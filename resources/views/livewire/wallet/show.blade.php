<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">
    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-5">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                    {{ $wallet->broker?->name ?? 'No broker' }}
                </p>
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    {{ $wallet->name }}
                </h1>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            <div class="flex flex-wrap gap-2">
                <span
                    class="inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700 dark:border-zinc-700 dark:text-zinc-200">
                    {{ $wallet->currency }}
                </span>

                <span
                    class="inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700 dark:border-zinc-700 dark:text-zinc-200">
                    Created {{ $wallet->created_at->format('d.m.Y') }}
                </span>

                @if ($wallet->last_transaction_date)
                    <span
                        class="inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700 dark:border-zinc-700 dark:text-zinc-200">
                        Last transaction {{ $wallet->last_transaction_date }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Assets</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $wallet->active_assets_count }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Transactions</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $wallet->transactions_count }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Invested</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                    {{ number_format($wallet->invested_total, 2, ',', ' ') }} {{ $wallet->currency }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Realized P/L</p>
                <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                    {{ number_format($wallet->realizedPL(), 2, ',', ' ') }} {{ $wallet->currency }}
                </p>
            </div>
        </div>
    </section>

    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Recent transactions</h2>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead
                    class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                    <tr>
                        <th class="px-2 py-2">Date</th>
                        <th class="px-2 py-2">Asset</th>
                        <th class="px-2 py-2">Type</th>
                        <th class="px-2 py-2 text-right">Quantity</th>
                        <th class="px-2 py-2 text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr class="border-b border-gray-100 dark:border-zinc-800">
                            <td class="px-2 py-2 text-gray-700 dark:text-zinc-200">
                                {{ $transaction->date->format('Y-m-d') }}
                            </td>
                            <td class="px-2 py-2 text-gray-700 dark:text-zinc-200">
                                {{ $transaction->asset?->symbol ?? '-' }}
                            </td>
                            <td class="px-2 py-2">
                                <span
                                    class="rounded px-2 py-0.5 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">
                                    {{ strtoupper($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-right text-gray-700 dark:text-zinc-200">
                                {{ abs($transaction->quantity) }}
                            </td>
                            <td class="px-2 py-2 text-right text-gray-700 dark:text-zinc-200">
                                {{ $transaction->price_per_unit }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-2 py-4 text-sm text-gray-500 dark:text-zinc-400">
                                No transactions in this wallet yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
