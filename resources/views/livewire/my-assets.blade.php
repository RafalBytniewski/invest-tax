<div>
<div>
    @php
    $groupedAssets = $assets->groupBy(function ($asset) {
        return strtoupper(mb_substr($asset->name, 0, 1));
    });
    @endphp
    @php
         $crypto = count($assets->where('asset_type', 'crypto'));
         $stock = count($assets->where('asset_type', 'stock'));
         $etf = count($assets->where('asset_type', 'etf'));

    @endphp 
    <div class="flex">
    <div class="p-3">
        <div>
            <label for="stocks">Stocks</label>
            <input type="checkbox" name="stocks" id="">
        </div>
        <div>
            <label for="etf">ETF</label>
            <input type="checkbox" name="etf" id="">
        </div>
        <div>
            <label for="crypto">Crypto</label>
            <input type="checkbox" name="crypto" id="">
        </div>
    </div>
    <div class="p-3">
        <div>
            <label for="stocks">PL</label>
            <input type="checkbox" name="stocks" id="">
        </div>
        <div>
            <label for="etf">USA</label>
            <input type="checkbox" name="etf" id="">
        </div>

    </div>
    </div>
    <div>
        <span class="px-2">Crypto: {{ $crypto }}</span>
        <span class="px-2">Stock: {{ $stock }}</span>
        <span class="px-2">ETF: {{ $etf }}</span>
    </div>
<div class="flex flex-wrap gap-2 mb-6 sticky top-0 bg-white py-2 z-10">
    @foreach($groupedAssets as $letter => $items)
        <a
            href="#letter-{{ $letter }}"
            class="px-2 py-1 text-sm font-medium rounded
                   text-blue-600 hover:bg-blue-100"
        >
            {{ $letter }}
        </a>
    @endforeach
</div>

</div>
<div class="space-y-8">
    @foreach($groupedAssets as $letter => $items)
        <div id="letter-{{ $letter }}">
            <h2 class="text-xl font-bold text-gray-800 mb-3">
                {{ $letter }}
            </h2>

            <ul class="space-y-1 pl-4">
                @foreach($items as $asset)
                    <li class="py-1 border-b border-gray-100">
                        {{ $asset->name }}
                        <span class="text-gray-500 font-bold">{{ $asset->asset_type}}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>

</div>
