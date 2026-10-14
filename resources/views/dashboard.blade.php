<x-layouts.app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
            <div class="tradingview-widget-container w-full" style="height: 46px;">
                <div class="tradingview-widget-container__widget" style="height: 46px;"></div>
                <div class="tradingview-widget-copyright px-3 py-1 text-xs text-neutral-500">
                    <a href="https://www.tradingview.com/markets/" rel="noopener nofollow" target="_blank">Market data by TradingView</a>
                </div>
                <script type="application/json" data-tradingview-widget data-src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js">
                    {
                        "symbols": [
                            { "proName": "FOREXCOM:SPXUSD", "title": "S&P 500" },
                            { "proName": "FOREXCOM:NSXUSD", "title": "Nasdaq 100" },
                            { "proName": "FOREXCOM:DJI", "title": "Dow Jones" },
                            { "proName": "GPW:WIG20", "title": "WIG20" },
                            { "proName": "FX:EURPLN", "title": "EUR/PLN" },
                            { "proName": "CMCMARKETS:GOLD", "title": "Gold" },
                            { "proName": "BITSTAMP:BTCUSD", "title": "Bitcoin" }
                        ],
                        "showSymbolLogo": true,
                        "colorTheme": "dark",
                        "isTransparent": false,
                        "displayMode": "adaptive",
                        "locale": "en"
                    }
                </script>
            </div>
        </div>

        <div>
            <div class="mb-3 flex items-center justify-between gap-4">
                <div>
                    <flux:heading size="lg">Market snapshot</flux:heading>
                    <flux:text class="mt-1">Najważniejsze indeksy i aktywa w jednym miejscu.</flux:text>
                </div>
                <span class="hidden text-xs text-neutral-500 sm:block dark:text-neutral-400">Powered by TradingView</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach([
                    ['symbol' => 'GPW:WIG20', 'name' => 'WIG20'],
                    ['symbol' => 'FOREXCOM:SPXUSD', 'name' => 'S&P 500'],
                    ['symbol' => 'FOREXCOM:NSXUSD', 'name' => 'Nasdaq 100'],
                    ['symbol' => 'INDEX:DEU40', 'name' => 'DAX'],
                ] as $market)
                    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
                        <div class="border-b border-neutral-200 px-4 py-3 dark:border-neutral-700">
                            <flux:heading size="sm">{{ $market['name'] }}</flux:heading>
                        </div>
                        <div class="tradingview-widget-container" style="height: 110px;">
                            <div class="tradingview-widget-container__widget" style="height: 110px;"></div>
                            <script type="application/json" data-tradingview-widget data-src="https://s3.tradingview.com/external-embedding/embed-widget-single-quote.js">
                                {
                                    "symbol": "{{ $market['symbol'] }}",
                                    "width": "100%",
                                    "colorTheme": "dark",
                                    "isTransparent": false,
                                    "locale": "en"
                                }
                            </script>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.65fr)_minmax(320px,1fr)]">
            <section class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
                <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-700">
                    <flux:heading size="lg">Market overview</flux:heading>
                    <flux:text class="mt-1">Indeksy, futures, obligacje i waluty.</flux:text>
                </div>
                <div class="tradingview-widget-container" style="height: 520px;">
                    <div class="tradingview-widget-container__widget" style="height: 520px;"></div>
                    <div class="tradingview-widget-copyright px-4 py-2 text-xs text-neutral-500">
                        <a href="https://www.tradingview.com/markets/" rel="noopener nofollow" target="_blank">World markets by TradingView</a>
                    </div>
                    <script type="application/json" data-tradingview-widget data-src="https://s3.tradingview.com/external-embedding/embed-widget-market-overview.js">
                        {
                            "colorTheme": "dark",
                            "dateRange": "12M",
                            "locale": "en",
                            "isTransparent": false,
                            "showFloatingTooltip": false,
                            "tabs": [
                                {
                                    "title": "Indices",
                                    "symbols": [
                                        { "s": "GPW:WIG20", "d": "WIG20" },
                                        { "s": "FOREXCOM:SPXUSD", "d": "S&P 500" },
                                        { "s": "FOREXCOM:NSXUSD", "d": "Nasdaq 100" },
                                        { "s": "FOREXCOM:DJI", "d": "Dow Jones" },
                                        { "s": "INDEX:DEU40", "d": "DAX" },
                                        { "s": "INDEX:NKY", "d": "Nikkei 225" }
                                    ]
                                },
                                {
                                    "title": "Forex",
                                    "symbols": [
                                        { "s": "FX:EURPLN", "d": "EUR/PLN" },
                                        { "s": "FX:EURUSD", "d": "EUR/USD" },
                                        { "s": "FX:USDPLN", "d": "USD/PLN" },
                                        { "s": "FX:USDJPY", "d": "USD/JPY" }
                                    ]
                                },
                                {
                                    "title": "Commodities",
                                    "symbols": [
                                        { "s": "CMCMARKETS:GOLD", "d": "Gold" },
                                        { "s": "TVC:USOIL", "d": "WTI Crude Oil" },
                                        { "s": "TVC:SILVER", "d": "Silver" }
                                    ]
                                }
                            ],
                            "showSymbolLogo": true,
                            "showChart": true
                        }
                    </script>
                </div>
            </section>

            <section class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900">
                <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-700">
                    <flux:heading size="lg">Economic calendar</flux:heading>
                    <flux:text class="mt-1">Najważniejsze wydarzenia makroekonomiczne.</flux:text>
                </div>
                <div class="tradingview-widget-container" style="height: 520px;">
                    <div class="tradingview-widget-container__widget" style="height: 520px;"></div>
                    <div class="tradingview-widget-copyright px-4 py-2 text-xs text-neutral-500">
                        <a href="https://www.tradingview.com/economic-calendar/" rel="noopener nofollow" target="_blank">Economic calendar by TradingView</a>
                    </div>
                    <script type="application/json" data-tradingview-widget data-src="https://s3.tradingview.com/external-embedding/embed-widget-events.js">
                        {
                            "colorTheme": "dark",
                            "isTransparent": false,
                            "locale": "en",
                            "countryFilter": "pl,eu,gb,us,de",
                            "importanceFilter": "1",
                            "width": "100%",
                            "height": "520"
                        }
                    </script>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
