<x-page-shell>
    <x-page-section>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span
                    class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                    Wallets
                </span>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Portfolio wallets overview
                </h1>
                <p class="mt-2 max-w-3xl text-sm text-gray-500 dark:text-zinc-400">
                    Same container width, same section rhythm and more readable wallet cards.
                </p>
            </div>
        </div>
    </x-page-section>

    <div class="space-y-6">
        @foreach ($wallets as $wallet)
            <x-page-section class="overflow-hidden">
                <div class="grid gap-6 2xl:grid-cols-[minmax(0,1fr)_380px]">
                    <div class="space-y-6">
                        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $wallet->name }}</h2>
                                <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                                    {{ $wallet->broker->name ?? 'No broker' }} · created {{ $wallet->created_at->format('Y-m-d') }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                    {{ $wallet->currency }}
                                </span>
                                <span
                                    class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                    {{ $wallet->transactions->count() }} transactions
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                                <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Active Assets</p>
                                <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ $wallet->activeAssetsCollection()->count() }}</p>
                            </div>

                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                                <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Transactions</p>
                                <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ $wallet->transactions->count() }}</p>
                            </div>

                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                                <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Cost Basis</p>
                                <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($wallet->transactions->sum('total_value'), 2, ',', ' ') }}
                                </p>
                                <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">{{ $wallet->currency }}</p>
                            </div>

                            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/60 dark:bg-blue-950/30">
                                <p class="text-xs font-medium uppercase tracking-[0.16em] text-blue-700 dark:text-blue-300">Realized P/L</p>
                                <p class="mt-3 text-2xl font-semibold text-blue-700 dark:text-blue-300">
                                    {{ number_format($wallet->realizedPL(), 2, ',', ' ') }}
                                </p>
                                <p class="mt-2 text-sm text-blue-700/80 dark:text-blue-300/80">{{ $wallet->currency }}</p>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-zinc-800">
                            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-zinc-800">
                                <thead>
                                    <tr class="text-left text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">
                                        <th class="px-4 py-3">Asset</th>
                                        <th class="px-4 py-3 text-right">Avg Price</th>
                                        <th class="px-4 py-3 text-right">Live Value</th>
                                        <th class="px-4 py-3 text-right">Transactions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-zinc-900">
                                    @foreach ($wallet->activeAssetsCollection() as $asset)
                                        @php
                                            $transactionsForAsset = $wallet->transactions->where('asset_id', $asset->id);
                                            $assetQuantity = $transactionsForAsset->sum('quantity');
                                            $assetPrice = $price[$asset->symbol] ?? null;
                                        @endphp
                                        <tr class="text-gray-700 dark:text-zinc-200">
                                            <td class="px-4 py-4">
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $asset->symbol }}@if ($asset->exchange)
                                                        .{{ $asset->exchange->symbol }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $asset->name }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                {{ number_format($wallet->averageBuyPrice($asset->id), 2, ',', ' ') }}
                                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $wallet->currency }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <button
                                                    wire:click="loadPrice('{{ $asset->symbol }}', '{{ $asset->exchange?->symbol }}')"
                                                    class="mr-2 rounded-lg border border-gray-200 px-2 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800">
                                                    Refresh
                                                </button>
                                                @if ($assetPrice !== null)
                                                    {{ number_format($assetPrice * $assetQuantity, 2, ',', ' ') }}
                                                    <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $wallet->currency }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <button wire:click="toggleTransactions({{ $asset->id }})"
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 font-medium text-blue-700 transition hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-950/30">
                                                    <span>{{ $transactionsForAsset->count() }}</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        @if (isset($visibleTransactions[$asset->id]) && $visibleTransactions[$asset->id])
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        @endif
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>

                                        @if (isset($visibleTransactions[$asset->id]) && $visibleTransactions[$asset->id])
                                            <tr>
                                                <td colspan="4" class="bg-gray-50 px-4 py-4 dark:bg-zinc-950/50">
                                                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-800">
                                                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-zinc-800">
                                                            <thead>
                                                                <tr class="text-left text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">
                                                                    <th class="px-4 py-3">Type</th>
                                                                    <th class="px-4 py-3 text-right">Quantity</th>
                                                                    <th class="px-4 py-3 text-right">Price</th>
                                                                    <th class="px-4 py-3 text-right">Total</th>
                                                                    <th class="px-4 py-3">Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100 dark:divide-zinc-900">
                                                                @foreach ($transactionsForAsset->sortByDesc('date') as $transaction)
                                                                    <tr class="text-gray-700 dark:text-zinc-200">
                                                                        <td class="px-4 py-3">
                                                                            <span
                                                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">
                                                                                {{ strtoupper($transaction->type) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="px-4 py-3 text-right">{{ number_format($transaction->quantity, 4, ',', ' ') }}</td>
                                                                        <td class="px-4 py-3 text-right">{{ number_format($transaction->price_per_unit, 2, ',', ' ') }}</td>
                                                                        <td class="px-4 py-3 text-right">{{ number_format($transaction->total_value, 2, ',', ' ') }}</td>
                                                                        <td class="px-4 py-3">{{ $transaction->date->format('Y-m-d') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Allocation</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Current chart container aligned with the card grid.</p>

                        <div class="mt-4" wire:ignore>
                            <canvas id="myChart-{{ $wallet->id }}"
                                style="background-color: #18181b; padding: 20px; display: inline-block; width: 100%; border-radius: 16px;"></canvas>
                        </div>

                        <script>
                            (function() {
                                const walletsData = {!! json_encode(
                                    $wallet->activeAssetsCollection()->map(
                                        fn($a) => [
                                            'id' => $a->id,
                                            'name' => $a->name,
                                            'amount' => $wallet->transactions()->where('asset_id', $a->id)->sum('total_value'),
                                        ],
                                    ),
                                ) !!};

                                const labels = walletsData.map(w => w.name);
                                const values = walletsData.map(w => w.amount);
                                const ctx = document.getElementById('myChart-{{ $wallet->id }}');

                                if (ctx) {
                                    new Chart(ctx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                data: values,
                                                backgroundColor: values.map((v, i) => `hsla(${i * 40}, 50%, 75%, 0.7)`),
                                                borderWidth: 0.5,
                                                hoverOffset: 5
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom',
                                                    labels: {
                                                        color: '#ffffff',
                                                        font: {
                                                            family: 'Inter',
                                                            size: 14,
                                                            weight: '600'
                                                        },
                                                        boxWidth: 20,
                                                        padding: 15,
                                                        generateLabels: (chart) => {
                                                            const defaultLabels = Chart.overrides.pie.plugins.legend.labels.generateLabels(chart);
                                                            const data = chart.data.datasets[0].data;
                                                            const total = data.reduce((a, b) => a + b, 0);

                                                            return defaultLabels.map((label, i) => {
                                                                const value = data[i];
                                                                const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                                                return {
                                                                    ...label,
                                                                    text: `${label.text} - ${percentage}%`,
                                                                };
                                                            });
                                                        }
                                                    }
                                                },
                                                tooltip: {
                                                    bodyFont: {
                                                        family: 'Inter',
                                                        size: 14
                                                    },
                                                    callbacks: {
                                                        label: function(context) {
                                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                            const value = context.parsed;
                                                            const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                                            return `${context.label}: ${value} (${percentage}%)`;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                            })();
                        </script>
                    </div>
                </div>
            </x-page-section>
        @endforeach
    </div>
</x-page-shell>
