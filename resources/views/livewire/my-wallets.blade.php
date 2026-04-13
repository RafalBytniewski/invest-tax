<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-6">
            <div class="space-y-2">
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Wallets
                </h1>
                <p class="max-w-3xl text-sm text-gray-500 dark:text-zinc-400">
                    Overview of all your wallets with aggregate metrics, individual allocation charts and the latest activity.
                </p>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Wallets</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ $summary['wallets_count'] }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Total number of portfolio containers.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Transactions</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ $summary['transactions_count'] }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Recorded operations across all wallets.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Active assets</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ $summary['active_assets_count'] }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Unique assets with open exposure.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Realized P/L</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">
                        {{ number_format($summary['realized_profit'], 2, ',', ' ') }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Summed without currency normalization.</p>
                </div>
            </div>

            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.3fr)_minmax(0,0.7fr)]">
                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Portfolio summary</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Currencies</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">{{ $summary['currencies'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Brokers</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">{{ $summary['brokers'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">Highlights</p>
                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Largest wallet</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                {{ $summary['largest_wallet'] }}
                                @if ($summary['largest_wallet_currency'] !== '')
                                    / {{ number_format($summary['largest_wallet_total'], 2, ',', ' ') }} {{ $summary['largest_wallet_currency'] }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Last activity</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">{{ $summary['last_activity'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @forelse ($wallets as $wallet)
        <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                                {{ $wallet->broker?->name ?? 'No broker' }}
                            </p>
                            <h2 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $wallet->name }}
                            </h2>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                Currency: {{ $wallet->currency }}
                            </span>
                            <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                Created: {{ $wallet->created_at->format('d.m.Y') }}
                            </span>
                            <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                Last transaction: {{ $wallet->last_transaction_date ?? 'No data' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[420px] xl:grid-cols-4">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Assets</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $wallet->active_assets_count }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Transactions</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $wallet->transactions_count }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Invested</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                                {{ number_format($wallet->invested_total, 2, ',', ' ') }} {{ $wallet->currency }}
                            </p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Realized P/L</p>
                            <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                                {{ number_format($wallet->realizedPL(), 2, ',', ' ') }} {{ $wallet->currency }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                    <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                                    Allocation
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                    Current chart based on net invested amount per active asset.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5">
                            @if ($wallet->chart_data->isNotEmpty())
                                <div wire:ignore>
                                    <canvas id="myChart-{{ $wallet->id }}" class="max-h-[360px]"></canvas>
                                </div>

                                <script>
                                    (function() {
                                        const walletChartData = @json($wallet->chart_data);
                                        const labels = walletChartData.map(item => item.name);
                                        const values = walletChartData.map(item => item.amount);
                                        const ctx = document.getElementById('myChart-{{ $wallet->id }}');

                                        if (!ctx) {
                                            return;
                                        }

                                        if (window.walletCharts && window.walletCharts['{{ $wallet->id }}']) {
                                            window.walletCharts['{{ $wallet->id }}'].destroy();
                                        }

                                        window.walletCharts = window.walletCharts || {};
                                        window.walletCharts['{{ $wallet->id }}'] = new Chart(ctx, {
                                            type: 'doughnut',
                                            data: {
                                                labels,
                                                datasets: [{
                                                    data: values,
                                                    backgroundColor: values.map((v, i) => `hsla(${i * 47}, 60%, 62%, 0.78)`),
                                                    borderWidth: 0,
                                                    hoverOffset: 6,
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom',
                                                        labels: {
                                                            color: '#a1a1aa',
                                                            font: {
                                                                family: 'Inter',
                                                                size: 12,
                                                                weight: '600'
                                                            },
                                                            padding: 16,
                                                        }
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(context) {
                                                                return `${context.label}: ${context.parsed}`;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    })();
                                </script>
                            @else
                                <div class="flex min-h-[280px] items-center justify-center rounded-xl border border-dashed border-gray-300 px-4 text-sm text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                    No active assets to display on the chart.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex h-full flex-col rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                                    Recent transactions
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                    Latest wallet activity in a simplified table.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 flex-1 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="max-h-[360px] overflow-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="sticky top-0 border-b border-gray-200 bg-white text-left text-xs uppercase text-gray-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400">
                                        <tr>
                                            <th class="px-3 py-3">Date</th>
                                            <th class="px-3 py-3">Asset</th>
                                            <th class="px-3 py-3">Type</th>
                                            <th class="px-3 py-3 text-right">Qty</th>
                                            <th class="px-3 py-3 text-right">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($wallet->recent_transactions->take(6) as $transaction)
                                            <tr class="border-b border-gray-100 dark:border-zinc-800">
                                                <td class="px-3 py-3 text-gray-700 dark:text-zinc-200">
                                                    {{ $transaction->date->format('Y-m-d') }}
                                                </td>
                                                <td class="px-3 py-3 text-gray-700 dark:text-zinc-200">
                                                    <div class="font-medium text-gray-900 dark:text-zinc-100">
                                                        {{ $transaction->asset?->name ?? 'Unknown asset' }}
                                                    </div>
                                                    @if ($transaction->asset?->symbol)
                                                        <div class="text-xs text-gray-500 dark:text-zinc-400">
                                                            {{ $transaction->asset->symbol }}@if ($transaction->asset?->exchange?->symbol)
                                                                .{{ $transaction->asset->exchange->symbol }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3">
                                                    <span class="rounded px-2 py-0.5 text-xs font-semibold uppercase {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">
                                                        {{ strtoupper($transaction->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-3 text-right text-gray-700 dark:text-zinc-200">
                                                    {{ rtrim(rtrim(number_format(abs($transaction->quantity), 8, '.', ' '), '0'), '.') }}
                                                </td>
                                                <td class="px-3 py-3 text-right text-gray-700 dark:text-zinc-200">
                                                    {{ number_format(abs($transaction->total_value), 2, ',', ' ') }}
                                                    <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-3 py-6 text-sm text-gray-500 dark:text-zinc-400">
                                                    No transactions in this wallet yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @empty
        <section class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">No wallets found</h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                Add your first wallet to start tracking allocation and recent transactions.
            </p>
        </section>
    @endforelse
</div>
