<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    {{-- ASSET DETAIL --}}
    <section
        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-6">
        <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $asset->name }}</h1>
                <p class="mt-1 text-sm uppercase tracking-wide text-gray-500 dark:text-zinc-400">
                    {{ $asset->symbol }}@if ($asset->exchange?->symbol)
                        .{{ $asset->exchange->symbol }}
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <span
                    class="rounded-full border border-gray-300 px-2.5 py-1 text-xs font-semibold uppercase text-gray-600 dark:border-zinc-600 dark:text-zinc-300">
                    {{ $asset->asset_type }}
                </span>
                @if ($asset->exchange)
                    <span
                        class="rounded-full border border-gray-300 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:border-zinc-600 dark:text-zinc-300">
                        {{ $asset->exchange->name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Latest close price</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    @if ($latestPrice)
                    {{ $latestPrice['close_price'] }}{{ $asset->exchange->currency }}
                         @else
                        -
                    @endif
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Asset currency</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $asset->exchange->currency }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400"></p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Current Value ({{ $latestPrice['date'] ?? '-' }})
                </p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $quantity * $latestPrice['close_price'] }}</p>
            </div>
        </div> --}}
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Holdings</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $quantity }}

                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Average buy prize</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $average }}
                    {{ $transactionCurrency }}{{-- dodac currency of wallet --}}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Current Profit/Loss</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                <p class="text-xs text-gray-500 dark:text-zinc-400">Relizaed Profit/Loss</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">-</p>
            </div>
        </div>
    </section>
    {{-- CHARTS --}}
<section
    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">Charts</h1>
    </div>

    <div wire:ignore>
        <div id="tv-container" class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const symbol = "{{$assetSymbol}}"; // 👈 z Blade

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
    <section
        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-6">
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
