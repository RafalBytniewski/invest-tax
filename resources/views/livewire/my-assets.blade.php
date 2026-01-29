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
                    {{-- WYKRES --}}
            <div wire:ignore class="mt-4">
                <canvas id="chart-asset-{{ $asset->id }}"></canvas>
            </div>
            @foreach ($asset->assetPrices as $price)
            {{$price->date}}    :
            {{$price->close_price}}
            @endforeach
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


// chart.js
document.addEventListener('livewire:load', () => {

    @this.assets.forEach(asset => {

        // jeśli asset nie ma cen – pomijamy
        if (!asset.asset_prices.length) return;

        const ctx = document
            .getElementById(`chart-asset-${asset.id}`)
            .getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: asset.asset_prices.map(p => p.date),
                datasets: [{
                    label: 'Close price',
                    data: asset.asset_prices.map(p => p.close_price),
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Dzień'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Close price'
                        }
                    }
                }
            }
        });

    });

});
</script>
