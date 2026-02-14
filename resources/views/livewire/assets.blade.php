<div class="space-y-6">
    @php
        $groupedAssets = $assets->groupBy(fn($asset) => strtoupper(mb_substr($asset->name, 0, 1)));

        $countsByType = [
            'stock' => $assets->where('asset_type', 'stock')->count(),
            'etf' => $assets->where('asset_type', 'etf')->count(),
            'crypto' => $assets->where('asset_type', 'crypto')->count(),
        ];

        $totalAssets = $assets->count();

        $chipBaseClasses = 'h-11 rounded-xl border px-4 text-sm font-semibold transition flex items-center justify-between gap-2';
        $chipInactiveClasses = 'border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-800 dark:text-zinc-100 hover:bg-gray-100 dark:hover:bg-zinc-700';
        $chipActiveClasses = 'border-blue-600 bg-blue-600 text-white dark:border-blue-500 dark:bg-blue-500';
    @endphp

    <section class="max-w-7xl mx-auto rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 space-y-4">
        <div class="grid gap-3 md:grid-cols-[1fr_auto] md:items-center">
            <div>
                <label for="asset-search" class="sr-only">Search assets</label>
                <input
                    id="asset-search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, symbol, or exchange"
                    class="w-full h-11 rounded-xl border border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 px-3 text-sm text-gray-900 dark:text-zinc-100 placeholder:text-gray-400 dark:placeholder:text-zinc-400 focus:border-blue-500 focus:ring-blue-500"
                />
            </div>
            <p class="text-sm text-gray-500 dark:text-zinc-400">
                Showing {{ $totalAssets }} {{ \Illuminate\Support\Str::plural('asset', $totalAssets) }}
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" role="group" aria-label="Asset type filters">
            <button
                wire:click="setType('')"
                aria-pressed="{{ $type === '' ? 'true' : 'false' }}"
                class="{{ $chipBaseClasses }} {{ $type === '' ? $chipActiveClasses : $chipInactiveClasses }}"
            >
                <span>All</span>
                <span class="text-xs {{ $type === '' ? 'text-white/80' : 'text-gray-500 dark:text-zinc-400' }}">{{ $totalAssets }}</span>
            </button>

            @foreach (['stock' => 'Stock', 'etf' => 'ETF', 'crypto' => 'Crypto'] as $filterType => $label)
                <button
                    wire:click="setType('{{ $filterType }}')"
                    aria-pressed="{{ $type === $filterType ? 'true' : 'false' }}"
                    class="{{ $chipBaseClasses }} {{ $type === $filterType ? $chipActiveClasses : $chipInactiveClasses }}"
                >
                    <span>{{ $label }}</span>
                    <span class="text-xs {{ $type === $filterType ? 'text-white/80' : 'text-gray-500 dark:text-zinc-400' }}">{{ $countsByType[$filterType] }}</span>
                </button>
            @endforeach
        </div>
    </section>

    @if ($totalAssets > 0)
        <nav class="max-w-7xl mx-auto sticky top-0 z-10 rounded-xl border border-gray-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 backdrop-blur px-3 py-2">
            <div class="flex flex-wrap gap-1.5">
                @foreach ($groupedAssets as $letter => $items)
                    <a
                        href="#letter-{{ $letter }}"
                        class="rounded-md px-2 py-1 text-xs font-semibold text-gray-600 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white"
                    >
                        {{ $letter }}
                        <span class="text-[10px] text-gray-400 dark:text-zinc-500">{{ $items->count() }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    @endif

    <section class="max-w-7xl mx-auto px-1 sm:px-0">
        @if ($totalAssets === 0)
            <div class="rounded-xl border border-dashed border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/60 p-8 text-center">
                @if (trim($search) !== '' || $type !== '')
                    <p class="text-sm font-medium text-gray-700 dark:text-zinc-200">No assets match this filter.</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Try clearing search or switching type.</p>
                    <button
                        wire:click="setType('')"
                        class="mt-4 rounded-lg border border-gray-300 dark:border-zinc-600 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700"
                    >
                        Reset filters
                    </button>
                @else
                    <p class="text-sm font-medium text-gray-700 dark:text-zinc-200">No assets available yet.</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Add assets in the admin panel to populate this list.</p>
                @endif
            </div>
        @else
            <div class="space-y-8">
                @foreach ($groupedAssets as $letter => $items)
                    <article id="letter-{{ $letter }}" class="scroll-mt-16">
                        <header class="mb-2 flex items-end justify-between border-b border-gray-200 dark:border-zinc-700 pb-2">
                            <h2 class="text-lg font-bold tracking-wide text-gray-800 dark:text-zinc-200">{{ $letter }}</h2>
                            <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $items->count() }} {{ \Illuminate\Support\Str::plural('asset', $items->count()) }}</span>
                        </header>

                        <ul class="divide-y divide-gray-100 dark:divide-zinc-700 rounded-lg border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                            @foreach ($items as $asset)
                                <li class="flex items-center justify-between gap-3 px-3 py-3 sm:px-4 hover:bg-gray-50 dark:hover:bg-zinc-800/70 transition">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100 truncate" title="{{ $asset->name }}">
                                            {{ $asset->name }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-zinc-400 uppercase tracking-wide">
                                            @if ($asset->asset_type === 'crypto')
                                                {{ $asset->symbol }}
                                            @else
                                                {{ $asset->symbol }}{{ $asset->exchange?->symbol ? '.' . $asset->exchange->symbol : '' }}
                                            @endif
                                        </p>
                                    </div>

                                    <span class="shrink-0 rounded-full border border-gray-300 dark:border-zinc-600 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-gray-600 dark:text-zinc-300">
                                        {{ $asset->asset_type }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
