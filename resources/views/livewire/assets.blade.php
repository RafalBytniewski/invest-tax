<div class="mx-auto w-full max-w-[1600px] space-y-6 px-4 py-4 sm:px-6 lg:px-8">

    @php
        $groupedAssets = $assets->groupBy(fn($asset) => strtoupper(mb_substr($asset->name, 0, 1)));

        $crypto = $assets->where('asset_type', 'crypto')->count();
        $stock = $assets->where('asset_type', 'stock')->count();
        $etf = $assets->where('asset_type', 'etf')->count();
    @endphp

    @php
        $baseClasses = 'rounded-xl font-semibold
        flex items-center justify-center transition cursor-pointer';

        $inactiveClasses = 'border border-gray-300 
        dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800
        text-gray-900 dark:text-zinc-100
        hover:bg-gray-300 dark:hover:bg-zinc-700';

        $activeClasses = 'bg-blue-600 text-white 
        border-blue-600 dark:bg-blue-500 dark:border-blue-500
        hover:bg-blue-300 dark:hover:bg-blue-700';
    @endphp



    <section class="bg-white dark:bg-zinc-900 border dark:border-zinc-800 rounded-xl px-6 py-6 mx-auto">

        <div class="grid gap-6 lg:grid-cols-[1fr_auto] items-start">

            <!-- LEFT SIDE -->
            <div>
                <span
                    class="inline-block rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                    Asset Directory
                </span>

                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Browse all tracked assets
                </h1>

                <p class="mt-2 max-w-xl text-sm text-gray-500 dark:text-zinc-400">
                    One consistent list with type filters, alphabet navigation and direct access to each asset page.
                </p>
            </div>

            <!-- FILTERS -->
            <div class="w-full max-w-[620px] justify-self-end space-y-5">

                <!-- TYPE FILTER -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <button wire:click="$set('type', 'stock')"
                        class="h-12 px-4 {{ $baseClasses }} {{ $type === 'stock' ? $activeClasses : $inactiveClasses }}">
                        <span>Stock</span>
                        <span class="text-xs mx-2">{{ $type === 'stock' ? $stock : '' }}</span>
                    </button>

                    <button wire:click="$set('type', 'etf')"
                        class="h-12 px-4 {{ $baseClasses }} {{ $type === 'etf' ? $activeClasses : $inactiveClasses }}">
                        <span>ETF</span>
                        <span class="text-xs mx-2">{{ $type === 'etf' ? $etf : '' }}</span>
                    </button>

                    <button wire:click="$set('type', 'crypto')"
                        class="h-12 px-4 {{ $baseClasses }} {{ $type === 'crypto' ? $activeClasses : $inactiveClasses }}">
                        <span>Crypto</span>
                        <span class="text-xs mx-2">{{ $type === 'crypto' ? $crypto : '' }}</span>
                    </button>

                    <button wire:click="$set('type', null)"
                        class="col-span-3 h-12 px-4 {{ $baseClasses }} {{ $type === null ? $activeClasses : $inactiveClasses }}">
                        <span>All</span>
                        <span class="text-xs mx-2">{{ $type === null ? $assets->count() : '' }}</span>
                    </button>
                </div>

                <!-- REGION -->
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-zinc-400 mb-2">
                        Region
                    </p>

                    <div class="flex flex-wrap gap-2">
                        @foreach (['NA', 'EU'] as $reg)
                            <button wire:click="$set('region', '{{ $reg }}')"
                                class="px-3 h-9 {{ $baseClasses }} {{ $reg === $region ? $activeClasses : $inactiveClasses }}">
                                {{ $reg }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- EXCHANGE -->
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-zinc-400 mb-2">
                        Exchange
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['GPW', 'NYSE', 'NASDAQ'] as $ex)
                            <button wire:click="$set('exchange', '{{ $ex }}')"
                                class="px-3 h-9 {{ $baseClasses }} {{ $ex === $exchange ? $activeClasses : $inactiveClasses }}">
                                {{ $ex }}
                            </button>
                        @endforeach
                    </div>
                </div>
                 <div class="justify-self-end">
                <button wire:click="resetFilters()" class="cursor-pointer underline">RESET FILTERS</button>
                 </div>
            </div>
        </div>
    </section>
    <!-- LETTER NAV -->
    <nav
        class="sticky top-0 z-10 bg-white dark:bg-zinc-900 border dark:border-zinc-800 rounded-xl px-4 py-4 space-y-5 mx-auto">
        <div class="flex flex-wrap justify-center gap-1.5">
            @foreach ($groupedAssets as $letter => $items)
                <a href="#letter-{{ $letter }}"
                    class="rounded-md px-3 py-1 text-xl font-semibold text-gray-600 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white">
                    {{ $letter }}
                </a>
            @endforeach
        </div>
    </nav>
    <!-- LIST -->
    <div class="space-y-10 mx-auto">
        @foreach ($groupedAssets as $letter => $items)
            <div id="letter-{{ $letter }}"
                class="scroll-mt-20 bg-white dark:bg-zinc-900 border dark:border-zinc-800 rounded-xl px-4 py-4 space-y-5 mx-auto">

                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                    {{ $letter }}
                </h2>

                <ul class="divide-y divide-gray-100 dark:divide-zinc-700">
                    @foreach ($items as $asset)
                        <li>
                            <a href="{{ route('assets.show', $asset->id) }}"
                                class="flex flex-col gap-3 rounded-xl px-2 py-4 transition hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between dark:hover:bg-zinc-950/50">
                                <div class="min-w-0">
                                    <div
                                        class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-zinc-500">
                                        @if ($asset->asset_type === 'crypto')
                                            {{ $asset->symbol }}
                                        @elseif ($asset->exchange_id)
                                            {{ $asset->symbol }}.{{ $asset->exchange->symbol }}
                                        @else
                                            {{ $asset->symbol }}
                                        @endif
                                    </div>
                                    <div class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                                        {{ $asset->name }}</div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($asset->exchange)
                                        <span
                                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">{{ $asset->exchange->symbol }}</span>
                                    @endif
                                    <span
                                        class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase text-gray-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $asset->asset_type }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

</div>
