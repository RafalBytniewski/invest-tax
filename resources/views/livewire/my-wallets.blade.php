<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">

    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-5">
            <div class="space-y-2">
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Wallets
                </h1>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Wallets --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Wallets</p>

                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        4
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        count
                    </p>

                </div>
                {{-- Largest --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Largest wallet</p>

                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        Test wallet
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        Broker
                    </p>

                </div>
                {{-- Best --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Best</p>

                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        Wallet name
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        PROFT
                    </p>

                </div>
                {{-- ?? --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">??</p>

                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        ??
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        ??
                    </p>

                </div>
            </div>
    </section>

    @foreach ($wallets as $wallet)
        <section
            class="rounded-xl border border-gray-200 bg-white my-2 p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
            <div class="flex flex-col gap-4 my-2 xl:flex-row xl:items-start xl:justify-between">
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
                        <span
                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            Currency: {{ $wallet->currency }}
                        </span>
                        <span
                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            Created: {{ $wallet->created_at->format('d.m.Y') }}
                        </span>
                        <span
                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            Last transaction: {{ $wallet->last_transaction_date ?? 'No data' }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[420px] xl:grid-cols-4">
                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <p class="text-xs text-gray-500 dark:text-zinc-400">Assets</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                            {{ $wallet->active_assets_count }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <p class="text-xs text-gray-500 dark:text-zinc-400">Transactions</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                            {{ $wallet->transactions_count }}</p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <p class="text-xs text-gray-500 dark:text-zinc-400">Invested</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                            {{ number_format($wallet->invested_total, 2, ',', ' ') }} {{ $wallet->currency }}
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                        <p class="text-xs text-gray-500 dark:text-zinc-400">Realized P/L</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                            {{ number_format($wallet->realizedPL(), 2, ',', ' ') }} {{ $wallet->currency }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                <div class="flex h-full flex-col rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <div>
                        @foreach ($wallet->activeAssetsCollection() as $asset)
                            @php
                                $transactionsForAsset = $wallet->transactions->where('asset_id', $asset->id);
                            @endphp
                            <div class="asset-section text-xs bg-neutral-800 border-b-1 border-gray-200">
                                <div class="grid grid-cols-4 items-center p-1 gap-2 ">
                                    <span title="{{ $asset->name }}">{{ $asset->symbol }}
                                        @if ($asset->exchange)
                                            .{{ $asset->exchange->symbol }}
                                        @endif
                                    </span>
                                    <span title="Average buy prize">Avg price:
                                        {{ round($wallet->averageBuyPrice($asset->id), 2) }}<span
                                            class="pl-1 text-[0.6rem] font-italic font-black font-rametto">{{ $wallet->currency }}</span></span>

                                    <span><button class="btn"
                                            wire:click="loadPrice('{{ $asset->symbol }}', '{{ $asset->exchange?->symbol }}')">Value:</button>
                                        {{ isset($price[$asset->symbol])
                                            ? $price[$asset->symbol] * $wallet->transactions->where('asset_id', $asset->id)->sum('quantity')
                                            : '-' }}

                                        {{-- {{ round($wallet->transactions()->where('asset_id', $asset->id)->sum('total_value'),2) }} --}}<span
                                            class="pl-1 text-[0.6rem] font-italic font-black font-rametto">{{ $wallet->currency }}</span></span>
                                    <button wire:click="toggleTransactions({{ $asset->id }})"
                                        class="flex items-center cursor-pointer gap-1">
                                        {{ $transactionsForAsset->count() }} transactions
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            @if (isset($visibleTransactions[$asset->id]))
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    </button>
                                </div>
                                @php
                                    $transactions = $wallet->transactions
                                        ->where('asset_id', $asset->id)
                                        ->sortByDesc('date');
                                @endphp
                                @if (isset($visibleTransactions[$asset->id]))
                                    <div class="transaction-table">
                                        <table class="w-full border-collapse">
                                            <thead>
                                                <tr class="bg-gray-800 text-white text-sm">
                                                    <th class="p-2 text-left">Type</th>
                                                    <th class="p-2 text-left">Quantity</th>
                                                    <th class="p-2 text-left">Price per unit</th>
                                                    <th class="p-2 text-left">Total value</th>
                                                    <th class="p-2 text-left">Date</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($transactions as $t)
                                                    <tr
                                                        class="text-sm border-b border-gray-700 
                                                            @if ($t->type === 'buy') bg-green-600/30 
                                                            @elseif($t->type === 'sell') bg-red-600/30 @endif">

                                                        <td class="p-2 capitalize">{{ $t->type }}</td>
                                                        <td class="p-2">{{ $t->quantity }}</td>
                                                        <td class="p-2">{{ $t->price_per_unit }}</td>
                                                        <td class="p-2">{{ $t->total_value }}</td>
                                                        <td class="p-2">{{ $t->date->format('d.m.Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <span class="text-2xl flex justify-center ">
                        Allocation
                    </span>
                    <div>
                        <div wire:ignore>
                            <canvas id="myChart-{{ $wallet->id }}" class="max-h-[500px]"></canvas>
                        </div>

                        <script>
                            (function() {
                                // Pobieramy dane z Blade (PHP)
                                const walletsData = {!! json_encode(
                                    $wallet->activeAssetsCollection()->map(
                                        fn($a) => [
                                            'id' => $a->id,
                                            'name' => $a->name,
                                            'amount' => $wallet->transactions()->where('asset_id', $a->id)->sum('total_value'), // ***ERROR*** - zmienic na sum('quantity')* avg('price_per_unit') a docelowo last_price dla assetu
                                        ],
                                    ),
                                ) !!};

                                // Tworzymy tablice do wykresu
                                const labels = walletsData.map(w => w.name);
                                const values = walletsData.map(w => w.amount);

                                // Pobieramy canvas
                                const ctx = document.getElementById('myChart-{{ $wallet->id }}');

                                // Jeśli canvas istnieje, rysujemy wykres
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
                                                        boxWidth: 20, // szerokość kwadratu przy labelu
                                                        padding: 15, // odstęp między labelami
                                                        generateLabels: (chart) => {
                                                            const defaultLabels = Chart.overrides.pie.plugins.legend.labels
                                                                .generateLabels(chart);
                                                            const data = chart.data.datasets[0].data;
                                                            const total = data.reduce((a, b) => a + b, 0);

                                                            return defaultLabels.map((label, i) => {
                                                                const value = data[i];
                                                                const percentage = ((value / total) * 100).toFixed(1);
                                                                return {
                                                                    ...label,
                                                                    text: `${label.text} – ${percentage}%`,
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
                                                            const percentage = ((value / total) * 100).toFixed(1);
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
            </div>
        </section>
    @endforeach
</div>
