<div class="mx-auto w-full max-w-[1600px] space-y-6 sm:px-6 lg:px-8">
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-5">
            <div>
                <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Assets
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                    Search your assets by name or symbol.
                </p>
            </div>

            <div class="relative">
                <label for="asset-search" class="sr-only">Search assets</label>
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-gray-400 dark:text-zinc-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.464 9.775l3.63 3.631a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                </svg>

                <input
                    id="asset-search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by symbol or name..."
                    class="h-12 w-full rounded-xl border border-gray-300 bg-gray-50 pl-12 pr-12 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-zinc-700 dark:bg-zinc-950/40 dark:text-white dark:placeholder:text-zinc-500"
                >

                @if ($search !== '')
                    <button
                        type="button"
                        wire:click="$set('search', '')"
                        class="absolute right-3 top-1/2 inline-flex size-7 -translate-y-1/2 items-center justify-center rounded-full text-gray-500 transition hover:bg-gray-200 hover:text-gray-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        aria-label="Clear search"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif

                @if (mb_strlen(trim($search)) >= 3)
                    <div class="absolute inset-x-0 top-full z-20 mt-2 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                        @if ($searchResults->isNotEmpty())
                            <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                                @foreach ($searchResults as $asset)
                                    <a href="{{ route('assets.show', $asset) }}" wire:navigate class="flex items-center justify-between gap-4 px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-zinc-800/70">
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-gray-900 dark:text-white">{{ $asset->name }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">
                                                {{ $asset->symbol }}
                                                @if ($asset->exchange)
                                                    <span class="text-gray-400 dark:text-zinc-500">· {{ $asset->exchange->symbol }}</span>
                                                @endif
                                            </p>
                                        </div>

                                        <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase text-gray-600 dark:bg-zinc-800 dark:text-zinc-400">
                                            {{ $asset->asset_type }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="px-4 py-4 text-sm text-gray-500 dark:text-zinc-400">
                                No assets found.
                            </p>
                        @endif
                    </div>
                @elseif ($search !== '')
                    <p class="absolute inset-x-0 top-full z-20 mt-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-500 shadow-lg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                        Type at least 3 characters to search.
                    </p>
                @endif
            </div>

            <p class="text-sm text-gray-500 dark:text-zinc-400">
                {{ $assets->count() }} {{ $assets->count() === 1 ? 'tracked asset' : 'tracked assets' }}
            </p>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-emerald-200 bg-emerald-50/40 shadow-sm dark:border-emerald-900/60 dark:bg-emerald-950/20">
        <div class="border-b border-emerald-200 px-4 py-4 dark:border-emerald-900/60 sm:px-6">
            <h2 class="text-xl font-bold text-emerald-800 dark:text-emerald-200">Your portfolio</h2>
            <p class="mt-1 text-sm text-emerald-700/80 dark:text-emerald-300/80">
                {{ $activeAssets->count() }} active {{ $activeAssets->count() === 1 ? 'asset' : 'assets' }} in your wallets.
            </p>
        </div>

        @if ($activeAssets->isNotEmpty())
            <div class="divide-y divide-emerald-200 dark:divide-emerald-900/60">
                @foreach ($activeAssets as $asset)
                    <a href="{{ route('assets.show', $asset) }}" wire:navigate class="flex items-center justify-between gap-4 px-4 py-4 transition hover:bg-emerald-100/70 dark:hover:bg-emerald-950/40 sm:px-6">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-emerald-950 dark:text-emerald-100">{{ $asset->name }}</p>
                            <p class="mt-1 text-sm font-medium text-emerald-700 dark:text-emerald-300">
                                {{ $asset->symbol }}
                                @if ($asset->exchange)
                                    <span class="text-emerald-600/70 dark:text-emerald-400/70">· {{ $asset->exchange->symbol }}</span>
                                @endif
                            </p>
                        </div>

                        <span class="shrink-0 rounded-full bg-emerald-200 px-3 py-1 text-xs font-semibold uppercase text-emerald-800 dark:bg-emerald-900/70 dark:text-emerald-200">
                            {{ $asset->asset_type }}
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="px-4 py-6 text-sm text-emerald-700/80 dark:text-emerald-300/80 sm:px-6">
                No active assets match your search.
            </p>
        @endif
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-gray-200 px-4 py-4 dark:border-zinc-800 sm:px-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Other assets</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                Assets you’ve traded but don’t currently hold.
            </p>
        </div>

        @if ($otherAssets->isNotEmpty())
            <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                @foreach ($otherAssets as $asset)
                    <a href="{{ route('assets.show', $asset) }}" wire:navigate class="flex items-center justify-between gap-4 px-4 py-4 transition hover:bg-gray-50 dark:hover:bg-zinc-950/50 sm:px-6">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-800 dark:text-zinc-200">{{ $asset->name }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">
                                {{ $asset->symbol }}
                                @if ($asset->exchange)
                                    <span class="text-gray-400 dark:text-zinc-500">· {{ $asset->exchange->symbol }}</span>
                                @endif
                            </p>
                        </div>

                        <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase text-gray-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ $asset->asset_type }}
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="px-4 py-6 text-sm text-gray-500 dark:text-zinc-400 sm:px-6">
                No other assets match your search.
            </p>
        @endif
    </section>
</div>
