<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">
    {{-- ASSET DETAIL --}}
    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6">
        @error('currency')
            <div class="mb-4 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200">
                {{ $message }}
            </div>
        @enderror
        @error('transactions')
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-950/30 dark:text-red-200">
                {{ $message }}
            </div>
        @enderror
        <div class="flex flex-col gap-5">
            <div class="space-y-2">
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    {{ $asset->name }}
                </h1>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            <div class="flex flex-wrap gap-2">
                <span
                    class="inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700 dark:border-zinc-700 dark:text-zinc-200">
                    {{ $asset->symbol }}@if ($asset->exchange?->symbol)
                        .{{ $asset->exchange->symbol }}
                    @endif
                </span>

                <span
                    class="inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700 dark:border-zinc-700 dark:text-zinc-200">
                    {{ $asset->asset_type }}
                </span>

                @if ($asset->exchange?->name)
                    <span
                        class="inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-gray-700 dark:border-zinc-700 dark:text-zinc-200">
                        {{ $asset->exchange->name }}
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Position Value --}}
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Position Value</p>

                @if ($positionValue !== null && $walletCurrency !== null)
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        {{ number_format($positionValue, 2, '.', ' ') }}
                        <span class="text-sm text-gray-500 dark:text-zinc-400">{{ $walletCurrency }}</span>
                        -
                        {{ $quantity }}
                        <span class="text-sm text-gray-500 dark:text-zinc-400">{{ $asset->symbol }}</span>
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        {{ $latestPrice?->date }}
                    </p>
                @else
                    <p class="text-xl font-semibold text-gray-400 dark:text-zinc-500">-</p>
                @endif
            </div>

            {{-- Average Buy Price --}}
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Average Buy Price</p>

                @if (is_numeric($average))
                    <p class="text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        {{ $average }}
                        <span class="text-sm text-gray-500 dark:text-zinc-400">{{ $walletCurrency }}</span>
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        based on {{ $buyTransaction }} buys
                    </p>
                @else
                    <p class="text-xl font-semibold text-gray-400 dark:text-zinc-500">-</p>
                @endif
            </div>

            {{-- Current P/L --}}
            <div class="rounded-xl border border-purple-200 bg-gray-50 p-4 dark:border-purple-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Current P/L</p>

                @if ($currentPL !== null && $positionValue !== null)
                    <p
                        class="text-xl font-semibold @if ($currentPL > 0) text-green-500 @elseif($currentPL < 0) text-red-500 @else text-gray-500 @endif">
                        {{ number_format($currentPL, 2, '.', ' ') }}
                        <span class="text-sm">{{ $walletCurrency }}</span>
                        @if ($costBasis > 0)
                            - {{ number_format(($currentPL / $costBasis) * 100, 2, '.', ' ') }} %
                        @endif
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        {{ $latestPrice?->date }}
                    </p>
                @else
                    <p class="text-xl font-semibold text-gray-400 dark:text-zinc-500">-</p>
                @endif
            </div>

            {{-- Realized P/L --}}
            <div class="rounded-xl border border-blue-200 bg-gray-50 p-4 dark:border-blue-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Realized P/L</p>

                @if ($realizedPL !== 0)
                    <p
                        class="text-xl font-semibold @if ($realizedPL > 0) text-green-500 @elseif($realizedPL < 0) text-red-500 @else text-gray-500 @endif">
                        {{ number_format($realizedPL, 2, '.', ' ') }}
                        <span class="text-sm">{{ $walletCurrency }}</span>
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        from {{ $sellTransaction }} sells
                    </p>
                @else
                    <p class="text-xl font-semibold text-gray-400 dark:text-zinc-500">-</p>
                @endif
            </div>
        </div>
    </section>
    {{-- CHARTS --}}
    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6" x-data="{ chartType: 'chartjs' }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-400">
                    Market chart
                </p>
                <h2 class="mt-1 text-2xl font-bold text-gray-900 dark:text-zinc-100">
                    {{ $asset->name }} price history
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                    Close prices from stored market data.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="text-xs font-medium text-gray-500 dark:text-zinc-400">Latest close</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-zinc-100">
                        @if ($latestPrice)
                            {{ number_format($latestPrice->close_price, 2, '.', ' ') }}
                            <span class="text-sm text-gray-500 dark:text-zinc-400">{{ $assetCurrency }}</span>
                        @else
                            -
                        @endif
                    </p>
                </div>

                {{-- Chart Switcher Toggle --}}
                <div class="inline-flex rounded-xl border border-gray-200 bg-gray-100 p-1 dark:border-zinc-800 dark:bg-zinc-950/60">
                    <button type="button"
                        @click="chartType = 'chartjs'; $nextTick(() => { window.initOrResizeAssetPriceChart && window.initOrResizeAssetPriceChart(); })"
                        :class="chartType === 'chartjs' ? 'bg-white text-gray-900 shadow-sm dark:bg-zinc-800 dark:text-white font-semibold' : 'text-gray-600 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-white font-medium'"
                        class="rounded-lg px-3 py-2 text-xs transition">
                        Chart.js
                    </button>
                    <button type="button"
                        @click="chartType = 'tradingview'; $nextTick(() => { window.initTradingViewWidget && window.initTradingViewWidget(); window.dispatchEvent(new Event('resize')); })"
                        :class="chartType === 'tradingview' ? 'bg-white text-gray-900 shadow-sm dark:bg-zinc-800 dark:text-white font-semibold' : 'text-gray-600 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-white font-medium'"
                        class="rounded-lg px-3 py-2 text-xs transition">
                        TradingView
                    </button>
                </div>
            </div>
        </div>

        {{-- TradingView Chart Container --}}
        <div x-show="chartType === 'tradingview'" x-cloak class="mt-6">
            <div wire:ignore class="overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-zinc-800 dark:bg-zinc-950/40">
                <div id="tv-container" class="tradingview-widget-container">
                    <div class="tradingview-widget-container__widget"></div>
                </div>
            </div>
        </div>

        <script>
            (() => {
                let tvLoaded = false;
                const initTradingView = () => {
                    const container = document.querySelector("#tv-container");
                    if (!container || tvLoaded || container.querySelector("iframe") || container.querySelector("script[src*='tradingview']")) {
                        return;
                    }
                    const symbol = "{{ $assetSymbol }}";
                    const config = {
                        "symbols": [
                            ["Asset", symbol + "|1D"]
                        ],
                        "chartType": "area",
                        "colorTheme": "dark",
                        "locale": "en",
                        "autosize": true,
                        "width": "100%",
                        "height": 400
                    };
                    const script = document.createElement("script");
                    script.src = "https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js";
                    script.async = true;
                    script.innerHTML = JSON.stringify(config);
                    container.appendChild(script);
                    tvLoaded = true;
                };

                window.initTradingViewWidget = initTradingView;

                if (document.readyState === 'loading') {
                    document.addEventListener("DOMContentLoaded", () => {
                        const tvContainer = document.querySelector("#tv-container");
                        if (tvContainer && tvContainer.closest('[x-show]')?.style.display !== 'none') {
                            initTradingView();
                        }
                    });
                }

                document.addEventListener('livewire:navigated', () => {
                    tvLoaded = false;
                    const tvContainer = document.querySelector("#tv-container");
                    if (tvContainer && tvContainer.closest('[x-show]')?.style.display !== 'none') {
                        initTradingView();
                    }
                });
            })();
        </script>

        {{-- Chart.js Chart Container --}}
        <div x-show="chartType === 'chartjs'" x-cloak class="mt-6">
            @if (!empty($chartData))
                <div wire:ignore class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                                Stored close price
                            </p>
                            <p id="asset-chart-range-label-{{ $asset->id }}" class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                All available data
                            </p>
                            <div class="mt-3 flex flex-wrap gap-4 text-xs font-medium text-gray-500 dark:text-zinc-400">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                    Buy transaction
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                    Sell transaction
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2" aria-label="Chart timeframe">
                            <button type="button" data-asset-chart-range="{{ $asset->id }}" data-days="30"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-emerald-400 hover:text-emerald-600 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-emerald-500 dark:hover:text-emerald-400">
                                1M
                            </button>
                            <button type="button" data-asset-chart-range="{{ $asset->id }}" data-days="90"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-emerald-400 hover:text-emerald-600 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-emerald-500 dark:hover:text-emerald-400">
                                3M
                            </button>
                            <button type="button" data-asset-chart-range="{{ $asset->id }}" data-days="180"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-emerald-400 hover:text-emerald-600 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-emerald-500 dark:hover:text-emerald-400">
                                6M
                            </button>
                            <button type="button" data-asset-chart-range="{{ $asset->id }}" data-days="365"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-emerald-400 hover:text-emerald-600 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-emerald-500 dark:hover:text-emerald-400">
                                1Y
                            </button>
                            <button type="button" data-asset-chart-range="{{ $asset->id }}" data-days="all"
                                class="rounded-lg border border-emerald-500 bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white transition">
                                ALL
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 h-[380px]">
                        <canvas id="asset-price-chart-{{ $asset->id }}"></canvas>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Range change</p>
                            <p id="asset-chart-change-{{ $asset->id }}" class="mt-1 text-base font-semibold">-</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">Low in range</p>
                            <p id="asset-chart-low-{{ $asset->id }}" class="mt-1 text-base font-semibold text-gray-900 dark:text-zinc-100">-</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-zinc-800 dark:bg-zinc-900">
                            <p class="text-xs text-gray-500 dark:text-zinc-400">High in range</p>
                            <p id="asset-chart-high-{{ $asset->id }}" class="mt-1 text-base font-semibold text-gray-900 dark:text-zinc-100">-</p>
                        </div>
                    </div>
                </div>

                <script>
                    (() => {
                        const renderAssetPriceChart = () => {
                            const chartData = @json($chartData);
                            const transactionData = @json($chartTransactions);
                            const canvas = document.getElementById('asset-price-chart-{{ $asset->id }}');
                            const currency = @json($assetCurrency) || '';
                            const buttons = document.querySelectorAll('[data-asset-chart-range="{{ $asset->id }}"]');
                            const rangeLabel = document.getElementById('asset-chart-range-label-{{ $asset->id }}');
                            const changeValue = document.getElementById('asset-chart-change-{{ $asset->id }}');
                            const lowValue = document.getElementById('asset-chart-low-{{ $asset->id }}');
                            const highValue = document.getElementById('asset-chart-high-{{ $asset->id }}');
                            let visibleChartData = chartData;

                            if (!canvas || !chartData.length || !window.Chart) {
                                return;
                            }

                            const money = new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            });
                            const percent = new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            });

                            const filterByRange = (days) => {
                                if (days === 'all') {
                                    return chartData;
                                }

                                const lastDate = new Date(chartData[chartData.length - 1].date);
                                const fromDate = new Date(lastDate);
                                fromDate.setDate(fromDate.getDate() - Number(days));

                                const filtered = chartData.filter(point => new Date(point.date) >= fromDate);
                                return filtered.length ? filtered : chartData;
                            };

                            const findClosestPointIndex = (data, transactionDate) => {
                                const target = new Date(transactionDate).getTime();

                                return data.reduce((closestIndex, point, index) => {
                                    const closestDistance = Math.abs(new Date(data[closestIndex].date).getTime() - target);
                                    const currentDistance = Math.abs(new Date(point.date).getTime() - target);

                                    return currentDistance < closestDistance ? index : closestIndex;
                                }, 0);
                            };

                            const transactionsForRange = (data, type) => {
                                const firstDate = new Date(data[0].date);
                                const lastDate = new Date(data[data.length - 1].date);

                                return transactionData
                                    .filter(transaction => transaction.type === type)
                                    .filter(transaction => {
                                        const date = new Date(transaction.date);

                                        return date >= firstDate && date <= lastDate;
                                    })
                                    .map(transaction => {
                                        const pointIndex = findClosestPointIndex(data, transaction.date);

                                        return {
                                            x: pointIndex,
                                            y: data[pointIndex].close_price,
                                            transaction,
                                        };
                                    });
                            };

                            const setActiveButton = (days) => {
                                buttons.forEach(button => {
                                    const isActive = button.dataset.days === String(days);
                                    button.classList.toggle('border-emerald-500', isActive);
                                    button.classList.toggle('bg-emerald-500', isActive);
                                    button.classList.toggle('text-white', isActive);
                                    button.classList.toggle('border-gray-200', !isActive);
                                    button.classList.toggle('text-gray-600', !isActive);
                                    button.classList.toggle('dark:border-zinc-700', !isActive);
                                    button.classList.toggle('dark:text-zinc-300', !isActive);
                                });
                            };

                            const updateSummary = (data, days) => {
                                const first = data[0];
                                const last = data[data.length - 1];
                                const values = data.map(point => point.close_price);
                                const low = Math.min(...values);
                                const high = Math.max(...values);
                                const change = last.close_price - first.close_price;
                                const changePercent = first.close_price !== 0 ? (change / first.close_price) * 100 : 0;
                                const isPositive = change >= 0;

                                rangeLabel.textContent = days === 'all'
                                    ? `${first.date} - ${last.date}`
                                    : `Last ${days} days: ${first.date} - ${last.date}`;

                                changeValue.textContent = `${isPositive ? '+' : ''}${money.format(change)} ${currency} (${isPositive ? '+' : ''}${percent.format(changePercent)}%)`;
                                changeValue.classList.toggle('text-emerald-500', isPositive);
                                changeValue.classList.toggle('text-rose-500', !isPositive);
                                lowValue.textContent = `${money.format(low)} ${currency}`;
                                highValue.textContent = `${money.format(high)} ${currency}`;
                            };

                            const xAxisHoverHighlight = {
                                id: 'xAxisHoverHighlight',
                                afterDraw(chart) {
                                    const activeElement = chart.getActiveElements()[0];

                                    if (!activeElement) {
                                        return;
                                    }

                                    const { ctx, chartArea, scales } = chart;
                                    const xScale = scales.x;
                                    const activeDataset = chart.data.datasets[activeElement.datasetIndex];
                                    const activePoint = activeDataset?.data?.[activeElement.index];
                                    const hoveredIndex = Math.round(Number(activePoint?.x ?? activeElement.index));
                                    const hoveredValue = Number(activePoint?.y);
                                    const label = visibleChartData[hoveredIndex]?.date;

                                    if (!label || Number.isNaN(hoveredValue)) {
                                        return;
                                    }

                                    const x = xScale.getPixelForValue(hoveredIndex);
                                    const y = scales.y.getPixelForValue(hoveredValue);
                                    const valueLabel = `${money.format(hoveredValue)} ${currency}`;
                                    ctx.save();
                                    ctx.font = '12px sans-serif';

                                    const labelWidth = ctx.measureText(label).width + 18;
                                    const labelHeight = 24;
                                    const valueLabelWidth = ctx.measureText(valueLabel).width + 18;
                                    const valueLabelHeight = 24;
                                    const labelX = Math.min(
                                        Math.max(x - labelWidth / 2, chartArea.left),
                                        chartArea.right - labelWidth,
                                    );
                                    const labelY = xScale.top + 6;
                                    const valueLabelX = Math.min(
                                        Math.max(scales.y.left + 4, 0),
                                        chart.width - valueLabelWidth - 2,
                                    );
                                    const valueLabelY = Math.min(
                                        Math.max(y - valueLabelHeight / 2, chartArea.top),
                                        chartArea.bottom - valueLabelHeight,
                                    );

                                    ctx.fillStyle = '#22c55e';
                                    ctx.beginPath();
                                    if (ctx.roundRect) {
                                        ctx.roundRect(labelX, labelY, labelWidth, labelHeight, 6);
                                    } else {
                                        ctx.rect(labelX, labelY, labelWidth, labelHeight);
                                    }
                                    ctx.fill();

                                    ctx.beginPath();
                                    if (ctx.roundRect) {
                                        ctx.roundRect(valueLabelX, valueLabelY, valueLabelWidth, valueLabelHeight, 6);
                                    } else {
                                        ctx.rect(valueLabelX, valueLabelY, valueLabelWidth, valueLabelHeight);
                                    }
                                    ctx.fill();

                                    ctx.fillStyle = '#ffffff';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    ctx.fillText(label, labelX + labelWidth / 2, labelY + labelHeight / 2);
                                    ctx.fillText(valueLabel, valueLabelX + valueLabelWidth / 2, valueLabelY + valueLabelHeight / 2);
                                    ctx.restore();
                                },
                            };

                            window.assetPriceCharts = window.assetPriceCharts || {};
                            window.assetPriceCharts['{{ $asset->id }}']?.destroy();

                            window.assetPriceCharts['{{ $asset->id }}'] = new Chart(canvas, {
                                type: 'line',
                                data: {
                                    datasets: [{
                                        label: `Close price (${currency})`,
                                        data: chartData.map((point, index) => ({
                                            x: index,
                                            y: point.close_price,
                                        })),
                                        borderColor: '#22c55e',
                                        backgroundColor: 'rgba(34, 197, 94, 0.12)',
                                        borderWidth: 2,
                                        tension: 0.25,
                                        pointRadius: 0,
                                        fill: true,
                                    }, {
                                        type: 'scatter',
                                        label: 'Buy',
                                        data: transactionsForRange(chartData, 'buy'),
                                        backgroundColor: '#22c55e',
                                        borderColor: '#ffffff',
                                        borderWidth: 2,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                    }, {
                                        type: 'scatter',
                                        label: 'Sell',
                                        data: transactionsForRange(chartData, 'sell'),
                                        backgroundColor: '#f43f5e',
                                        borderColor: '#ffffff',
                                        borderWidth: 2,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    layout: {
                                        padding: {
                                            bottom: 8,
                                        },
                                    },
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    scales: {
                                        x: {
                                            type: 'linear',
                                            min: 0,
                                            max: Math.max(chartData.length - 1, 0),
                                            ticks: {
                                                color: '#a1a1aa',
                                                maxRotation: 0,
                                                autoSkip: true,
                                                maxTicksLimit: 8,
                                                callback: value => chartData[Math.round(value)]?.date ?? '',
                                            },
                                            grid: {
                                                color: 'rgba(161, 161, 170, 0.12)',
                                            },
                                        },
                                        y: {
                                            position: 'right',
                                            ticks: {
                                                color: '#a1a1aa',
                                                callback: value => `${money.format(value)} ${currency}`,
                                            },
                                            grid: {
                                                color: 'rgba(161, 161, 170, 0.12)',
                                            },
                                        },
                                    },
                                    plugins: {
                                        legend: {
                                            display: false,
                                            labels: {
                                                color: '#d4d4d8',
                                            },
                                        },
                                        tooltip: {
                                            displayColors: false,
                                            mode: 'nearest',
                                            intersect: true,
                                            filter: context => context.datasetIndex !== 0,
                                            callbacks: {
                                                title: items => {
                                                    const item = items[0];
                                                    const transaction = item?.raw?.transaction;

                                                    return transaction?.date ?? visibleChartData[Math.round(item?.parsed.x)]?.date ?? '';
                                                },
                                                label: context => {
                                                    const transaction = context.raw?.transaction;

                                                    if (transaction) {
                                                        return `${transaction.type.toUpperCase()}   ${transaction.quantity} x ${money.format(transaction.price)} ${transaction.currency}`;
                                                    }

                                                    return `Close: ${money.format(context.parsed.y)} ${currency}`;
                                                },
                                            },
                                        },
                                    },
                                },
                                plugins: [xAxisHoverHighlight],
                            });

                            const updateChart = (days) => {
                                const data = filterByRange(days);
                                const chart = window.assetPriceCharts['{{ $asset->id }}'];
                                if (!chart) return;
                                visibleChartData = data;

                                chart.data.datasets[0].data = data.map((point, index) => ({
                                    x: index,
                                    y: point.close_price,
                                }));
                                chart.data.datasets[1].data = transactionsForRange(data, 'buy');
                                chart.data.datasets[2].data = transactionsForRange(data, 'sell');
                                chart.options.scales.x.max = Math.max(data.length - 1, 0);
                                chart.options.scales.x.ticks.callback = value => data[Math.round(value)]?.date ?? '';
                                chart.update();

                                setActiveButton(days);
                                updateSummary(data, days);
                            };

                            buttons.forEach(button => {
                                button.addEventListener('click', () => updateChart(button.dataset.days));
                            });

                            updateChart('all');
                        };

                        window.initOrResizeAssetPriceChart = () => {
                            if (!window.assetPriceCharts || !window.assetPriceCharts['{{ $asset->id }}']) {
                                renderAssetPriceChart();
                            } else {
                                window.assetPriceCharts['{{ $asset->id }}'].resize();
                            }
                        };

                        if (document.readyState === 'loading') {
                            document.addEventListener("DOMContentLoaded", renderAssetPriceChart);
                        } else {
                            renderAssetPriceChart();
                        }
                        document.addEventListener('livewire:navigated', renderAssetPriceChart);
                    })();
                </script>
            @else
                <div class="flex min-h-[280px] items-center justify-center rounded-xl border border-dashed border-gray-300 px-4 text-sm text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                    No historical prices to display on the chart.
                </div>
            @endif
        </div>
    </section>
    {{-- TRANSACTIONS --}}
    <section class="rounded-xl bg-white dark:bg-zinc-900 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Your recent transactions</h2>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead
                    class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
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
                            <td class="px-2 py-2 text-gray-700 dark:text-zinc-200">
                                {{ $transaction->date->format('Y-m-d') }}</td>
                            <td class="px-2 py-2">
                                <span
                                    class="rounded px-2 py-0.5 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">
                                    {{ strtoupper($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-right text-gray-700 dark:text-zinc-200">
                                {{ abs($transaction->quantity) }}</td>
                            <td class="px-2 py-2 text-right text-gray-700 dark:text-zinc-200">
                                {{ $transaction->price_per_unit }}
                                <span
                                    class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                            </td>
                            <td class="px-2 py-2 text-gray-700 dark:text-zinc-200">
                                {{ $transaction->wallet?->name ?? '-' }}
                                <span
                                    class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->wallet?->broker?->name ? '(' . $transaction->wallet->broker->name . ')' : '' }}</span>
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
