<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <a
        href="{{ route('assets') }}"
        wire:navigate
        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
    >
        Back to assets
    </a>

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-6">
        <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $asset->name }}</h1>
                <p class="mt-1 text-sm uppercase tracking-wide text-gray-500 dark:text-zinc-400">
                    {{ $asset->symbol }}@if($asset->exchange?->symbol).{{ $asset->exchange->symbol }}@endif
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="rounded-full border border-gray-300 px-2.5 py-1 text-xs font-semibold uppercase text-gray-600 dark:border-zinc-600 dark:text-zinc-300">
                    {{ $asset->asset_type }}
                </span>
                @if ($asset->exchange)
                    <span class="rounded-full border border-gray-300 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:border-zinc-600 dark:text-zinc-300">
                        {{ $asset->exchange->name }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Latest close</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    @if ($latestPrice)
                        {{ number_format($latestPrice['value'], 6, '.', ' ') }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Latest price date</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $latestPrice['date'] ?? '-' }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Price source</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $latestPrice['source'] ?? '-' }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Price points</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $pricePoints }}</p>
            </div>
        </div>
    </section>

    <section
        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-6"
        x-data="{
            mode: @js($pricePoints > 0 ? 'database' : ($tradingViewEmbedUrl ? 'tradingview' : 'none')),
            initDbChart() {
                const canvas = document.getElementById('asset-price-chart-{{ $asset->id }}');
                if (!canvas || typeof Chart === 'undefined') return;

                let labels = [];
                let values = [];
                try {
                    labels = JSON.parse(canvas.dataset.labels || '[]');
                    values = JSON.parse(canvas.dataset.values || '[]');
                } catch (e) {
                    return;
                }

                window.assetPriceCharts = window.assetPriceCharts || {};
                const chartKey = 'asset-{{ $asset->id }}';
                if (window.assetPriceCharts[chartKey]) {
                    window.assetPriceCharts[chartKey].destroy();
                }

                window.assetPriceCharts[chartKey] = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Close price',
                            data: values,
                            borderColor: '#2563eb',
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0.2,
                            fill: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        scales: { x: { ticks: { maxTicksLimit: 10 } } }
                    }
                });
            },
            switchMode(nextMode) {
                this.mode = nextMode;
                if (nextMode === 'database') this.$nextTick(() => this.initDbChart());
            }
        }"
        x-init="
            if (mode === 'database') initDbChart();
        "
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Price chart</h2>

            <div class="inline-flex rounded-lg border border-gray-300 bg-gray-50 p-1 dark:border-zinc-700 dark:bg-zinc-800">
                <button
                    type="button"
                    @click="switchMode('tradingview')"
                    @disabled(!$tradingViewEmbedUrl)
                    :class="mode === 'tradingview' ? 'bg-blue-600 text-white dark:bg-blue-500' : 'text-gray-700 hover:bg-gray-100 dark:text-zinc-200 dark:hover:bg-zinc-700'"
                    class="rounded-md px-3 py-1.5 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                >
                    TradingView
                </button>
                <button
                    type="button"
                    @click="switchMode('database')"
                    @disabled($pricePoints === 0)
                    :class="mode === 'database' ? 'bg-blue-600 text-white dark:bg-blue-500' : 'text-gray-700 hover:bg-gray-100 dark:text-zinc-200 dark:hover:bg-zinc-700'"
                    class="rounded-md px-3 py-1.5 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Chart.js (DB)
                </button>
            </div>
        </div>

        <div class="mt-4" wire:ignore x-show="mode === 'tradingview'" x-cloak>
            <iframe
                src="{{ $tradingViewEmbedUrl }}"
                title="TradingView chart for {{ $asset->name }}"
                class="h-[420px] w-full rounded-md border border-gray-200 dark:border-zinc-700"
                frameborder="0"
                allowtransparency="true"
                scrolling="no"
                loading="lazy"
            ></iframe>
        </div>

        <div class="mt-4" wire:ignore x-show="mode === 'database'" x-cloak>
            <div class="h-[260px] max-h-[260px] w-full">
                <canvas
                    id="asset-price-chart-{{ $asset->id }}"
                    data-labels='@json($chartLabels)'
                    data-values='@json($chartValues)'
                    class="h-full w-full"
                ></canvas>
            </div>
        </div>

        <p
            x-show="mode === 'none'"
            x-cloak
            class="mt-3 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
        >
            Selected chart mode is unavailable for this asset.
        </p>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Your recent transactions</h2>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                    <tr>
                        <th class="px-2 py-2">Date</th>
                        <th class="px-2 py-2">Type</th>
                        <th class="px-2 py-2 text-right">Quantity</th>
                        <th class="px-2 py-2 text-right">Price</th>
                        <th class="px-2 py-2">Wallet</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr class="border-b border-gray-100 dark:border-zinc-800">
                            <td class="px-2 py-2 text-gray-700 dark:text-zinc-200">{{ $transaction->date->format('Y-m-d') }}</td>
                            <td class="px-2 py-2">
                                <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">
                                    {{ strtoupper($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-right text-gray-700 dark:text-zinc-200">{{ number_format(abs($transaction->quantity), 8, '.', ' ') }}</td>
                            <td class="px-2 py-2 text-right text-gray-700 dark:text-zinc-200">
                                {{ number_format((float) $transaction->price_per_unit, 6, '.', ' ') }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                            </td>
                            <td class="px-2 py-2 text-gray-700 dark:text-zinc-200">
                                {{ $transaction->wallet?->name ?? '-' }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->wallet?->broker?->name ? '(' . $transaction->wallet->broker->name . ')' : '' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-2 py-4 text-sm text-gray-500 dark:text-zinc-400">
                                No transactions for this asset in your wallets yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
