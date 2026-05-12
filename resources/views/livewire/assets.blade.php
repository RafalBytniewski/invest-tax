<div id="assets-top" class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">

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

        $activeClasses = 'border-slate-900 bg-slate-900 text-white
        shadow-sm shadow-slate-900/15 hover:bg-slate-800
        dark:border-zinc-100 dark:bg-zinc-100 dark:text-zinc-900
        dark:hover:bg-zinc-200';
    @endphp

    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-6">
            <div class="space-y-2">
                <p class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Assets
                </p>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-800"></div>

            {{-- RESET FILTERS --}}
            <div class="flex items-center justify-end">
                <button wire:click="resetFilters()"
                    class="inline-flex h-12 w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-5 text-sm font-semibold transition hover:border-rose-300 hover:bg-rose-50 text-rose-700 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-rose-500/50 dark:hover:bg-rose-500/10 dark:text-rose-300 xl:w-auto">
                    Reset filters
                </button>
            </div>
            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.4fr)_minmax(0,0.8fr)_minmax(0,1fr)_auto] xl:items-end">
                {{-- TYPE FILTER --}}
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                        Asset type
                    </p>

                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
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
                            class="h-12 px-4 {{ $baseClasses }} {{ $type === null ? $activeClasses : $inactiveClasses }}">
                            <span>All</span>
                            <span class="text-xs mx-2">{{ $type === null ? $assets->count() : '' }}</span>
                        </button>
                    </div>
                </div>

                {{-- REGION --}}
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                        Region
                    </p>

                    <div class="flex flex-wrap gap-2">
                        @foreach (['NA', 'EU'] as $reg)
                            <button wire:click="$set('region', '{{ $reg }}')"
                                class="min-w-[5rem] px-5 h-11 {{ $baseClasses }} {{ $reg === $region ? $activeClasses : $inactiveClasses }}">
                                {{ $reg }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- EXCHANGE  --}}
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-zinc-800 dark:bg-zinc-950/40">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-zinc-400">
                        Exchange
                    </p>

                    <div class="flex flex-wrap gap-2">
                        @foreach (['GPW', 'NYSE', 'NASDAQ'] as $ex)
                            <button wire:click="$set('exchange', '{{ $ex }}')"
                                class="px-5 h-11 {{ $baseClasses }} {{ $ex === $exchange ? $activeClasses : $inactiveClasses }}">
                                {{ $ex }}
                            </button>
                        @endforeach
                    </div>
                </div>


            </div>
        </div>
    </section>
    {{-- LETTER NAV --}}
    <nav
        class="sticky top-0 z-10 bg-white dark:bg-zinc-900 border dark:border-zinc-800 rounded-xl px-4 py-4 space-y-5 mx-auto">
        <div class="flex items-center gap-3">
            <div class="flex flex-1 flex-wrap justify-center gap-1.5">
                @foreach ($groupedAssets as $letter => $items)
                    <a href="#letter-{{ $letter }}"
                        class="rounded-md px-3 py-1 text-xl font-semibold text-gray-600 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>

            <a href="#assets-top"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-300 bg-slate-50 text-slate-700 transition hover:border-slate-500 hover:bg-slate-100 hover:text-slate-950 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:border-zinc-500 dark:hover:bg-zinc-700 dark:hover:text-white"
                aria-label="Back to top"
                title="Back to top">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a.75.75 0 0 1 .53.22l4.25 4.25a.75.75 0 1 1-1.06 1.06L10.75 5.56V16a.75.75 0 0 1-1.5 0V5.56L6.28 8.53a.75.75 0 1 1-1.06-1.06l4.25-4.25A.75.75 0 0 1 10 3Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </nav>
    {{-- LIST --}}
    <div class="space-y-10 mx-auto">
        <div
            class="scroll-mt-20 bg-white dark:bg-zinc-900 border border-emerald-200 dark:border-emerald-900/60 rounded-xl px-4 py-4 space-y-5 mx-auto">

            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-bold text-emerald-800 dark:text-emerald-200">
                    Active
                </h2>
                <p class="text-sm text-emerald-700/80 dark:text-emerald-300/80">
                    {{ $activeAssets->count() }} active assets in current view
                </p>
            </div>

            @if ($activeAssets->isNotEmpty())
                <ul class="divide-y divide-emerald-100 dark:divide-emerald-950/60">
                    @foreach ($activeAssets as $asset)
                        <li>
                            <a href="{{ route('assets.show', $asset->id) }}"
                                class="flex flex-col gap-3 rounded-xl px-2 py-4 transition hover:bg-emerald-50/70 sm:flex-row sm:items-center sm:justify-between dark:hover:bg-emerald-950/20">
                                <div class="min-w-0">
                                    <div
                                        class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-500 dark:text-emerald-400">
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
                                            class="rounded-full border border-emerald-200 px-3 py-1 text-xs font-medium text-emerald-700 dark:border-emerald-900/60 dark:text-emerald-300">{{ $asset->exchange->symbol }}</span>
                                    @endif
                                    <span
                                        class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ $asset->asset_type }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-emerald-700/80 dark:text-emerald-300/80">
                    No active assets match current filters.
                </p>
            @endif
        </div>

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