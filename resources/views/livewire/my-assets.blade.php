<div class="space-y-6">

    @php
        $groupedAssets = $assets->groupBy(fn($asset) => strtoupper(mb_substr($asset->name, 0, 1)));

        $crypto = $assets->where('asset_type', 'crypto')->count();
        $stock  = $assets->where('asset_type', 'stock')->count();
        $etf    = $assets->where('asset_type', 'etf')->count();
    @endphp

    

    <!-- FILTER PANEL -->
    <div class="bg-white dark:bg-zinc-900 border dark:border-zinc-800 rounded-xl px-4 py-4 space-y-5 max-w-7xl mx-auto">

        <!-- MAIN FILTER GRID -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

            <!-- STOCK -->
            <button wire:click="$set('type', 'stock')" class="h-12 px-4 rounded-xl border border-gray-300 dark:border-zinc-700
                bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100
                font-semibold flex items-center justify-between
                hover:bg-gray-100 dark:hover:bg-zinc-700 transition">
                <span>Stock</span>
                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $stock }}</span>
            </button>

            <!-- ETF -->
            <button wire:click="$set('type', 'etf')" class="h-12 px-4 rounded-xl border border-gray-300 dark:border-zinc-700
                bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100
                font-semibold flex items-center justify-between
                hover:bg-gray-100 dark:hover:bg-zinc-700 transition">
                <span>ETF</span>
                <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $etf }}</span>
            </button>

            <!-- CRYPTO -->
            <button wire:click="$set('type', 'crypto')" class="h-12 px-4 rounded-xl border border-purple-300 dark:border-purple-500/40
                bg-gray-50 dark:bg-zinc-800 text-gray-900 dark:text-zinc-100
                font-semibold flex items-center justify-between
                hover:bg-purple-50 dark:hover:bg-purple-500/10 transition
                sm:row-span-2">
                <span>Crypto</span>
                <span class="text-xs text-purple-600 dark:text-purple-400">{{ $crypto }}</span>
            </button>

            <!-- ALL -->
            <button wire:click="$set('type', null)" class="h-12 px-4 rounded-xl
                bg-blue-600 text-white font-semibold
                flex items-center justify-between
                hover:bg-blue-500 transition
                col-span-2 sm:col-span-3">
                <span>All</span>
                <span class="text-xs text-white/70">{{ $assets->count() }}</span>
            </button>

        </div>
    </div>

    <!-- LETTER NAV -->
    <div class="bg-white dark:bg-zinc-900 border-b dark:border-zinc-800 px-4 py-2 flex flex-wrap gap-2">
        @foreach($groupedAssets as $letter => $items)
            <a href="#letter-{{ $letter }}"
               class="px-2 py-1 text-sm font-semibold rounded-md
                      text-gray-600 dark:text-zinc-300
                      hover:bg-gray-100 dark:hover:bg-zinc-800
                      hover:text-gray-900 dark:hover:text-white">
                {{ $letter }}
            </a>
        @endforeach
    </div>

<!-- LIST -->
<div class="space-y-10 max-w-7xl mx-auto px-4">
    @foreach($groupedAssets as $letter => $items)
        <div id="letter-{{ $letter }}">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                {{ $letter }}
            </h2>

            <ul class="divide-y divide-gray-100 dark:divide-zinc-700">
                @foreach($items as $asset)
                    <li class="py-2 flex justify-between items-center">
                        <div>
                        <span class=" font-semibold uppercase text-gray-400 dark:text-zinc-400">
                            @if($asset->asset_type == 'crypto')
                                {{ $asset->symbol}}
                            @elseif($asset->exchange_id)
                                {{ $asset->symbol }}.{{ $asset->exchange->symbol }}
                            @endif
                            </span>
                            {{ $asset->name }}
                        
                        </div>
                        <div>
                        <span class="text-xs font-semibold uppercase text-gray-400 dark:text-zinc-400">
                            {{ $asset->asset_type }}
                        </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>

</div>
