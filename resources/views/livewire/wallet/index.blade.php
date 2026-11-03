<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">
    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-5">
            <div class="space-y-2">
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">Wallets</h1>
            </div>
            <div class="border-t border-gray-200 dark:border-zinc-800"></div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Wallets</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">{{ $wallets->count() }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">count</p>
                </div>
            </div>
        </div>
    </section>

    @foreach ($wallets as $wallet)
        <section class="my-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
            <div class="my-2 flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">{{ $wallet->broker?->name ?? 'No broker' }}</p>
                        <a href="{{ route('my-wallet.show', $wallet) }}" wire:navigate class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $wallet->name }}</a>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">Currency: {{ $wallet->currency }}</span>
                        <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">Created: {{ $wallet->created_at->format('d.m.Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-6">
                <div class="rounded-xl border border-gray-200 bg-gray-50/80 p-5 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <span class="flex justify-center text-2xl">Portfolio value</span>

                    @if (count($wallet->chart_data ?? []) > 0)
                        <div wire:ignore class="mt-4 h-[320px]"><canvas id="wallet-chart-{{ $wallet->id }}"></canvas></div>
                        <script>
                            (() => {
                                const canvas = document.getElementById('wallet-chart-{{ $wallet->id }}');
                                const data = {!! json_encode($wallet->chart_data ?? []) !!};
                                if (!canvas || !window.Chart) return;
                                window.Chart.getChart(canvas)?.destroy();
                                new window.Chart(canvas, {
                                    type: 'line',
                                    data: {
                                        labels: data.map((point) => point.date),
                                        datasets: [{
                                            data: data.map((point) => point.value),
                                            borderColor: '#3b82f6',
                                            backgroundColor: 'rgba(59, 130, 246, 0.12)',
                                            borderWidth: 2,
                                            pointRadius: 0,
                                            pointHoverRadius: 4,
                                            tension: 0.25,
                                            fill: true,
                                        }],
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        interaction: { mode: 'index', intersect: false },
                                        scales: {
                                            x: { ticks: { color: '#a1a1aa', maxTicksLimit: 8, maxRotation: 0 }, grid: { color: 'rgba(161, 161, 170, 0.12)' } },
                                            y: { beginAtZero: true, ticks: { color: '#a1a1aa' }, grid: { color: 'rgba(161, 161, 170, 0.12)' } },
                                        },
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                displayColors: false,
                                                callbacks: {
                                                    label: (context) => `${Number(context.raw).toLocaleString('pl-PL', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} {{ $wallet->currency }}`,
                                                },
                                            },
                                        },
                                    },
                                });
                            })();
                        </script>
                    @else
                        <p class="mt-6 text-center text-sm text-gray-500 dark:text-zinc-400">No portfolio history available yet.</p>
                    @endif
                </div>
            </div>
        </section>
    @endforeach
</div>
