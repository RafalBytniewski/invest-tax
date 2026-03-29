<x-page-shell>
    @php
        $latestClose = $latestPrice?->close_price;
        $positionValue = is_numeric($latestClose) ? $latestClose * $quantity : null;
        $averageValue = is_numeric($average) ? $average : null;
        $unrealizedPl = $positionValue !== null && $averageValue !== null ? $positionValue - ($averageValue * $quantity) : null;
        $priceDate = $latestPrice?->date?->format('Y-m-d') ?? ($latestPrice?->date ?? '-');
    @endphp

    <x-page-section>
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px] xl:items-start">
            <div class="space-y-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                            Asset Detail
                        </span>
                        <span
                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium uppercase text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                            {{ $asset->asset_type }}
                        </span>
                        @if ($asset->exchange)
                            <span
                                class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                                {{ $asset->exchange->name }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                            {{ $asset->name }}
                        </h1>
                        <p class="mt-2 text-sm uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                            {{ $asset->symbol }}@if ($asset->exchange?->symbol)
                                .{{ $asset->exchange->symbol }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Latest Close</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $latestClose !== null ? number_format($latestClose, 2, ',', ' ') : '-' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                            {{ $transactionCurrency ?? $asset->exchange?->currency ?? '-' }} · {{ $priceDate }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Position Size</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format($quantity, 4, ',', ' ') }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                            {{ $asset->symbol }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Average Buy Price</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $averageValue !== null ? number_format($averageValue, 2, ',', ' ') : '-' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                            {{ $transactionCurrency ?? $asset->exchange?->currency ?? '-' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
                        <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Position Value</p>
                        <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $positionValue !== null ? number_format($positionValue, 2, ',', ' ') : '-' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                            {{ $transactionCurrency ?? $asset->exchange?->currency ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-emerald-700 dark:text-emerald-300">Unrealized P/L</p>
                    <p class="mt-3 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">
                        {{ $unrealizedPl !== null ? number_format($unrealizedPl, 2, ',', ' ') : '-' }}
                    </p>
                    <p class="mt-2 text-sm text-emerald-700/80 dark:text-emerald-300/80">
                        {{ $transactionCurrency ?? $asset->exchange?->currency ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/60 dark:bg-blue-950/30">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-blue-700 dark:text-blue-300">Realized P/L</p>
                    <p class="mt-3 text-2xl font-semibold text-blue-700 dark:text-blue-300">
                        {{ is_numeric($realizedPL) ? number_format($realizedPL, 2, ',', ' ') : '-' }}
                    </p>
                    <p class="mt-2 text-sm text-blue-700/80 dark:text-blue-300/80">
                        {{ $transactionCurrency ?? $asset->exchange?->currency ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </x-page-section>

    <x-page-section>
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Price Chart</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">TradingView overview for {{ $asset->symbol }}</p>
            </div>
        </div>

        <div wire:ignore class="overflow-hidden rounded-2xl border border-gray-200 bg-zinc-950 dark:border-zinc-800">
            <div id="tv-container" class="tradingview-widget-container min-h-[420px]">
                <div class="tradingview-widget-container__widget"></div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const symbol = @js($assetSymbol);
                const container = document.querySelector('#tv-container');

                if (!container || container.dataset.loaded === 'true') {
                    return;
                }

                container.dataset.loaded = 'true';

                const config = {
                    symbols: [
                        ['Asset', symbol + '|1D']
                    ],
                    chartType: 'area',
                    colorTheme: 'dark',
                    locale: 'en',
                    autosize: true,
                    width: '100%',
                    height: 420,
                };

                const script = document.createElement('script');
                script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js';
                script.async = true;
                script.innerHTML = JSON.stringify(config);

                container.appendChild(script);
            });
        </script>
    </x-page-section>

    <x-page-section>
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Last 10 operations for this asset across your wallets.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-zinc-800">
                <thead>
                    <tr class="text-left text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3 text-right">Quantity</th>
                        <th class="px-4 py-3 text-right">Price</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Wallet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-900">
                    @forelse ($transactions as $transaction)
                        <tr class="text-gray-700 dark:text-zinc-200">
                            <td class="px-4 py-4">{{ $transaction->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->type === 'sell' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' }}">
                                    {{ strtoupper($transaction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">{{ number_format(abs($transaction->quantity), 4, ',', ' ') }}</td>
                            <td class="px-4 py-4 text-right">
                                {{ number_format($transaction->price_per_unit, 2, ',', ' ') }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                {{ number_format(abs($transaction->total_value), 2, ',', ' ') }}
                                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->currency }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $transaction->wallet?->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction->wallet?->broker?->name ?? '' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-zinc-400">
                                No transactions for this asset in your wallets yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-page-section>
</x-page-shell>
