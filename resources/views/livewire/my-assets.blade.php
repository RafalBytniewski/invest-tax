<div>
    @foreach ($assets as $asset)
        @php
            $quantity = $asset->transactions->sum('quantity');
            $transactionValue = $asset->transactions->sum('total_value');
            $currency = $asset->transactions->first()?->currency ?? '';
        @endphp

        @if ($quantity !== 0)
            <span class="flex flex-row my-2">
                @if ($asset->image)
                    <img class="mx-2" src="{{ asset('storage/' . $asset->image) }}" width="80" height="80" alt="Image">
                @endif

                <div class="flex flex-col p-4 rounded">
                    <!-- Dane assetu -->
                    <span class="flex flex-row items-center gap-2">
                        {{ $asset->name }} — Quantity: {{ $quantity }} — 
                        Transactions value: {{ number_format($transactionValue, 2, ',', ' ') }} {{ $currency }}
                    </span>

                    <!-- Obliczenia -->
                    <span class="flex flex-row items-center gap-2 mt-2">
                        Quantity:
                        <span id="quantity-{{ $asset->id }}">{{ $quantity }}</span> ×
                        <input type="number" id="currentPrice-{{ $asset->id }}" placeholder="Enter current price" class="border rounded px-1">

                        <button type="button" 
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded"
                                onclick="calculateValue({{ $asset->id }}, {{ $transactionValue }})">
                            Count
                        </button>

                        Current value =
                        <span id="current-value-{{ $asset->id }}">0</span> {{ $currency }}

                        <span id="percent-change-{{ $asset->id }}" style="display:none">0%</span>
                    </span>
                </div>
            </span>
        @endif
    @endforeach
</div>

<script>
function calculateValue(assetId, transactionValue) {
    const quantity = parseFloat(document.getElementById(`quantity-${assetId}`).textContent) || 0;
    const price = parseFloat(document.getElementById(`currentPrice-${assetId}`).value) || 0;
    const currentValue = quantity * price;

    // Aktualizacja wartości
    document.getElementById(`current-value-${assetId}`).textContent = currentValue.toFixed(2);

    // Wyliczenie różnicy procentowej
    if (transactionValue !== 0) {
        const diff = currentValue - transactionValue;
        const percentChange = (diff / transactionValue) * 100;

        const percentSpan = document.getElementById(`percent-change-${assetId}`);
        percentSpan.style.display = 'block';
        percentSpan.textContent = (percentChange > 0 ? '+' : '') + percentChange.toFixed(2) + '%';
        percentSpan.textContent += ' - ' + diff.toFixed(2) + ' PLN';
        percentSpan.style.color = percentChange > 0 ? 'green' : (percentChange < 0 ? 'red' : 'black');
    }
}
</script>
