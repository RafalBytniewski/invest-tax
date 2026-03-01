<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <p class="p-2">Assets value: 9999 USD</p>
                <div class="p-2">Assets:
                    <p>Stock: 24</p>
                    <p>Crypto: 4</p>
                    <p>ETF: 2</p>

                </div>
                <p class="p-2"></p>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="p-2">
                    <p class="p-2">TRANSACTIONS</p>
                    <p class="p-2">Last month: 5</p>
                    <p class="p-2">Last year: 23</p>
                    <p class="p-2">All: 320</p>

                </div>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    <p class="p-2">Realized P/L</p>
                    <p class="p-2">Last year P/L</p>
                    <p class="p-2">Last year tax</p>

            </div>
        </div>
        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <p class="p-2">Wykres kolowy: stocks, etf, asset albo sektorowy</p>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="p-2">
                    wykres liniowy zmiany wartosci portfela
                </div>
            </div>

        </div>
    </div>

</x-layouts.app>
