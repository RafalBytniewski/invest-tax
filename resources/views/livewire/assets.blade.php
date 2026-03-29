<div class="mx-auto w-full max-w-[1600px] space-y-6 px-4 py-4 sm:px-6 lg:px-8">
    @php
        $groupedAssets = $assets->groupBy(fn ($asset) => strtoupper(mb_substr($asset->name, 0, 1)));
        $counts = ['stock' => $assets->where('asset_type', 'stock')->count(), 'etf' => $assets->where('asset_type', 'etf')->count(), 'crypto' => $assets->where('asset_type', 'crypto')->count(), 'all' => $assets->count()];
        $filters = [['key' => 'stock', 'label' => 'Stock'], ['key' => 'etf', 'label' => 'ETF'], ['key' => 'crypto', 'label' => 'Crypto'], ['key' => null, 'label' => 'All']];
    @endphp

    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">Asset Directory</span>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Browse all tracked assets</h1>
                <p class="mt-2 max-w-3xl text-sm text-gray-500 dark:text-zinc-400">One consistent list with type filters, alphabet navigation and direct access to each asset page.</p>
            </div>

            <div class="grid w-full gap-3 sm:grid-cols-2 xl:w-[560px] xl:grid-cols-4">
                @foreach ($filters as $filter)
                    @php
                        $isActive = $type === $filter['key'];
                        $countKey = $filter['key'] ?? 'all';
                    @endphp
                    <button wire:click="$set('type', @js($filter['key']))"
                        class="{{ $isActive ? 'border-blue-600 bg-blue-600 text-white dark:border-blue-500 dark:bg-blue-500' : 'border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-zinc-800 dark:bg-zinc-950/50 dark:text-zinc-100 dark:hover:bg-zinc-800' }} flex h-14 items-center justify-between rounded-2xl border px-4 text-left text-sm font-semibold transition">
                        <span>{{ $filter['label'] }}</span>
                        <span class="text-xs {{ $isActive ? 'text-white/80' : 'text-gray-500 dark:text-zinc-400' }}">{{ $counts[$countKey] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <div class="sticky top-0 z-10">
        <section class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-wrap gap-1.5">
                @foreach ($groupedAssets as $letter => $items)
                    <a href="#letter-{{ $letter }}" class="rounded-xl px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">{{ $letter }}</a>
                @endforeach
            </div>
        </section>
    </div>

    <div class="space-y-6">
        @foreach ($groupedAssets as $letter => $items)
            <section id="letter-{{ $letter }}" class="scroll-mt-24 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
                <div class="mb-5 flex items-center justify-between gap-3 border-b border-gray-100 pb-4 dark:border-zinc-800">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $letter }}</h2>
                    <span class="text-sm text-gray-500 dark:text-zinc-400">{{ $items->count() }} items</span>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-zinc-800">
                    @foreach ($items as $asset)
                        <li>
                            <a href="{{ route('assets.show', $asset->id) }}" class="flex flex-col gap-3 rounded-xl px-2 py-4 transition hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between dark:hover:bg-zinc-950/50">
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-zinc-500">
                                        @if ($asset->asset_type === 'crypto')
                                            {{ $asset->symbol }}
                                        @elseif ($asset->exchange_id)
                                            {{ $asset->symbol }}.{{ $asset->exchange->symbol }}
                                        @else
                                            {{ $asset->symbol }}
                                        @endif
                                    </div>
                                    <div class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $asset->name }}</div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($asset->exchange)
                                        <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-500 dark:border-zinc-700 dark:text-zinc-400">{{ $asset->exchange->symbol }}</span>
                                    @endif
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase text-gray-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $asset->asset_type }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
</div>
