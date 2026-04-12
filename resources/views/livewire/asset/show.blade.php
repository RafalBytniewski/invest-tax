<div class="mx-auto max-w-[1600px] space-y-6 p-4 sm:p-6">
    {{-- ASSET DETAIL --}}
    <section class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 sm:p-6">
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
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Position Value</p>

                @if ($positionValue !== null)
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

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Current P/L</p>

                @if ($currentPL !== 0 && $positionValue !== null)
                    <p
                        class="text-xl font-semibold @if ($currentPL > 0) text-green-500 @elseif($currentPL < 0) text-red-500 @else text-gray-500 @endif">
                        {{ number_format($currentPL, 2, '.', ' ') }}
                        <span class="text-sm">{{ $walletCurrency }}</span>
                        -
                        {{ abs(number_format($currentPL / $positionValue, 2, '.', ' ') * 100) }} %
                    </p>

                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                        {{ $latestPrice?->date }}
                    </p>
                @else
                    <p class="text-xl font-semibold text-gray-400 dark:text-zinc-500">-</p>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Realized P/L</p>

                @if ($realizedPL !== 0)
                    <p
                        class="text-xl font-semibold @if ($realizedPL > 0) text-green-500 @elseif($currentPL < 0) text-red-500 @else text-gray-500 @endif">
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
    <section class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 sm:p-6">
        <div class="space-y-2">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">Chart</h2>
            <p class="text-sm text-gray-500 dark:text-zinc-400">
                TradingView snapshot for the selected asset.
            </p>
        </div>

        <div class="mt-4" wire:ignore>
            <div id="tv-container" class="tradingview-widget-container">
                <div class="tradingview-widget-container__widget"></div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
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

                document
                    .querySelector("#tv-container")
                    .appendChild(script);
            });
        </script>
    </section>

    {{-- TRANSACTIONS --}}
    <section class="rounded-xl bg-white p-4 shadow-sm dark:bg-zinc-900 sm:p-6">
        <div class="space-y-2">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Your recent transactions</h2>
      
        </div>

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
                                {{ $transaction->date->format('Y-m-d') }}
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
