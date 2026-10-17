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
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">4</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">count</p>
                </div>
                {{-- Largest --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Largest wallet</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">Test wallet</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Broker</p>
                </div>
                {{-- Best --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Best</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">Wallet name</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">PROFT</p>
                </div>
                {{-- ?? --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">??</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">??</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">??</p>
                </div>
            </div>
        </div>
    </section>

    @foreach ($wallets as $walletData)
        @php
            $wallet = $walletData['wallet'];
            $overview = $walletData['overview'];
        @endphp

        <section class="my-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                            {{ $wallet->broker?->name ?? 'No broker' }}
                        </p>
                        <a href="{{ route('my-wallet.show', $wallet) }}" wire:navigate
                            class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $wallet->name }}
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            Currency: {{ $wallet->currency }}
                        </span>
                        <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            Created: {{ $wallet->created_at->format('d.m.Y') }}
                        </span>
                        <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            Last price: {{ $overview['last_price_date'] ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Active assets</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $overview['active_assets_count'] }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Portfolio value</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                        {{ $overview['portfolio_value'] === null ? '-' : number_format($overview['portfolio_value'], 2, ',', ' ').' '.$wallet->currency }}
                    </p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Cost basis</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                        {{ number_format($overview['cost_basis'], 2, ',', ' ') }} {{ $wallet->currency }}
                    </p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Unrealized P/L</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                        {{ $overview['unrealized_pl'] === null ? '-' : number_format($overview['unrealized_pl'], 2, ',', ' ').' '.$wallet->currency }}
                    </p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Realized P/L</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-zinc-100">
                        {{ number_format($overview['realized_pl'], 2, ',', ' ') }} {{ $wallet->currency }}
                    </p>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                <h2 class="flex justify-center text-2xl text-gray-900 dark:text-zinc-100">Allocation</h2>

                @if (count($overview['allocation']) > 0)
                    <div wire:ignore>
                        <canvas id="myChart-{{ $wallet->id }}" class="max-h-[500px]"></canvas>
                    </div>

                    <script>
                        (() => {
                            const allocation = {{ \Illuminate\Support\Js::from($overview['allocation']) }};
                            const canvas = document.getElementById('myChart-{{ $wallet->id }}');

                            if (!canvas) {
                                return;
                            }

                            new Chart(canvas, {
                                type: 'doughnut',
                                data: {
                                    labels: allocation.map((item) => item.name),
                                    datasets: [{
                                        data: allocation.map((item) => item.value),
                                        backgroundColor: allocation.map((item, index) => `hsla(${index * 40}, 50%, 75%, 0.7)`),
                                        borderWidth: 0.5,
                                        hoverOffset: 5,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                color: '#ffffff',
                                                font: { family: 'Inter', size: 14, weight: '600' },
                                                padding: 15,
                                                generateLabels: (chart) => {
                                                    const labels = Chart.overrides.pie.plugins.legend.labels.generateLabels(chart);
                                                    return labels.map((label, index) => ({
                                                        ...label,
                                                        text: `${label.text} – ${allocation[index].percentage.toFixed(1)}%`,
                                                    }));
                                                },
                                            },
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: (context) => `${context.label}: ${context.parsed.toFixed(2)} {{ $wallet->currency }}`,
                                            },
                                        },
                                    },
                                },
                            });
                        })();
                    </script>
                @else
                    <p class="mt-6 text-center text-sm text-gray-500 dark:text-zinc-400">
                        No price data available.
                    </p>
                @endif
            </div>
        </section>
    @endforeach
</div>
