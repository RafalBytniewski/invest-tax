<div>

{{-- 

<div>
    <div>
        NVIDIA - cena otwarcia: 
        @if($cdProjectOpenPrice)
            {{ number_format($cdProjectOpenPrice, 2) }} PLN
        @else
            Brak danych
        @endif
    </div>

    <button wire:click="loadPrice">Odśwież cenę</button>
</div> --}}

    @foreach ($wallets as $wallet)
    {{--     @php
            $assets = $wallet->transactions->pluck('asset')->unique('id')->values();
        @endphp --}}
        <div
            class="relative p-5 my-5 mr-50 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 h-auto bg-[#1f1f1f] bg-[repeating-linear-gradient(135deg,#2a2a2a_0px,#2a2a2a_1px,transparent_1px,transparent_8px)]">
            <div class="flex">
                <div class="flex flex-col flex-3">
                    <div class="mx-12 my-6">
                        <div class="pt-3 rounded-t-lg border-b-2 border-gray-200 bg-neutral-700">
                            <span class="text-2xl flex justify-center ">
                                {{ $wallet->name }}
                            </span>
                        </div>
                        <div class="p-2 flex justify-around bg-gray-800">
                            <span>Currency: {{ $wallet->currency }}</span>
                            <span>Broker/Exchange: {{ $wallet->broker->name }}</span>
                            <span>Owner: -</span>
                            <span>Date: {{ $wallet->created_at->format('d.m.Y') }}</span> {{-- DODAC METODe- 1 transakcja->date --}}
                        </div>
                    </div>
                    <div class="mx-12 mb-6">
                        <div class="p-2 flex justify-around bg-gray-800">
                            <span>Assets: {{ $wallet->assetsCollection()->count() }}</span>
                            <span>Transactions: {{ $wallet->transactions->count() }}</span>
                            <span>Cost: {{ $wallet->transactions->sum('total_value') }}<span class="pl-1 text-[0.6rem] font-italic font-black font-rametto">{{ $wallet->currency}}</span></span>
                            <span>Current value:</span>
                            <span>Profit: {{ round($wallet->realizedPL(),2) }}@if($wallet->realizedPL() !== 0)<span class="pl-1 text-[0.6rem] font-italic font-black font-rametto">{{ $wallet->currency}}</span>@endif</span>

                        </div>
                    </div>
                    <div class="mx-12">
                        <div>
                            @foreach ($wallet->assetsCollection() as $asset)
                                @php
                                    $transactionsForAsset = $wallet->transactions->where('asset_id', $asset->id);
                                @endphp
                                <div class="asset-section text-xs bg-neutral-800 border-b-1 border-gray-200">
                                    <div class="grid grid-cols-4 items-center p-1 gap-2 ">
                                        <span title="{{ $asset->name }}">{{ $asset->symbol}}
                                            @if ($asset->exchange)
                                                .{{ $asset->exchange->symbol }}
                                            @endif
                                        </span>
                                        <span title="Average buy prize">Avg price: {{ round($wallet->averageBuyPrice($asset->id), 2) }}<span class="pl-1 text-[0.6rem] font-italic font-black font-rametto">{{ $wallet->currency}}</span></span>

                                        <span><button class="btn" wire:click="loadPrice('{{ $asset->symbol }}', '{{ $asset->exchange?->symbol }}')">Value:</button> {{ isset($price[$asset->symbol])
    ? $price[$asset->symbol] * $wallet->transactions->where('asset_id', $asset->id)->sum('quantity')
    : '-' }}
                                            
                                            {{-- {{ round($wallet->transactions()->where('asset_id', $asset->id)->sum('total_value'),2) }} --}}<span class="pl-1 text-[0.6rem] font-italic font-black font-rametto">{{ $wallet->currency}}</span></span>
                                        <button wire:click="toggleTransactions({{ $asset->id }})"
                                            class="flex items-center cursor-pointer gap-1">
                                            {{ $transactionsForAsset->count() }} transactions
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                @if (isset($visibleTransactions[$asset->id]))
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 15l7-7 7 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                @endif
                                            </svg>
                                        </button>
                                    </div>
                                    @php
                                        $transactions = $wallet->transactions
                                            ->where('asset_id', $asset->id)
                                            ->sortByDesc('date');
                                    @endphp
                                    @if (isset($visibleTransactions[$asset->id]))
                                        <div class="transaction-table">
                                            <table class="w-full border-collapse">
                                                <thead>
                                                    <tr class="bg-gray-800 text-white text-sm">
                                                        <th class="p-2 text-left">Type</th>
                                                        <th class="p-2 text-left">Quantity</th>
                                                        <th class="p-2 text-left">Price per unit</th>
                                                        <th class="p-2 text-left">Total value</th>
                                                        <th class="p-2 text-left">Date</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($transactions as $t)
                                                        <tr
                                                            class="text-sm border-b border-gray-700 
                                                            @if ($t->type === 'buy') bg-green-600/30 
                                                            @elseif($t->type === 'sell') bg-red-600/30 @endif">

                                                            <td class="p-2 capitalize">{{ $t->type }}</td>
                                                            <td class="p-2">{{ $t->quantity }}</td>
                                                            <td class="p-2">{{ $t->price_per_unit }}</td>
                                                            <td class="p-2">{{ $t->total_value }}</td>
                                                            <td class="p-2">{{ $t->date->format('d.m.Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex-2 mx-12 my-6">
                    <div class="pt-3 rounded-t-lg border-b-2 border-gray-200 bg-neutral-700">
                        <span class="text-2xl flex justify-center ">
                            Pie Chart
                        </span>
                    </div>
                    <div>
                        <div>
                            <div wire:ignore>
                                <canvas id="myChart-{{ $wallet->id }}"
                                    style="background-color: #262626; padding: 20px; display: inline-block; border-radius: 8px;"></canvas>
                            </div>

                            <script>
                                (function() {
                                    // Pobieramy dane z Blade (PHP)
                                    const walletsData = {!! json_encode(
                                        $wallet->assetsCollection()->map(
                                            fn($a) => [
                                                'id' => $a->id,
                                                'name' => $a->name,
                                                'amount' => $wallet->transactions()->where('asset_id', $a->id)->sum('total_value'),
                                            ],
                                        ),
                                    ) !!};

                                    // Tworzymy tablice do wykresu
                                    const labels = walletsData.map(w => w.name);
                                    const values = walletsData.map(w => w.amount);

                                    // Pobieramy canvas
                                    const ctx = document.getElementById('myChart-{{ $wallet->id }}');

                                    // Jeśli canvas istnieje, rysujemy wykres
                                    if (ctx) {
                                        new Chart(ctx, {
                                            type: 'doughnut',
                                            data: {
                                                labels: labels,
                                                datasets: [{
                                                    data: values,
                                                    backgroundColor: values.map((v, i) => `hsla(${i * 40}, 50%, 75%, 0.7)`),
                                                    borderWidth: 0.5,
                                                    hoverOffset: 5
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom',
                                                        labels: {
                                                            color: '#ffffff',
                                                            font: {
                                                                family: 'Inter',
                                                                size: 14,
                                                                weight: '600'
                                                            },
                                                            boxWidth: 20, // szerokość kwadratu przy labelu
                                                            padding: 15, // odstęp między labelami
                                                            generateLabels: (chart) => {
                                                                const defaultLabels = Chart.overrides.pie.plugins.legend.labels
                                                                    .generateLabels(chart);
                                                                const data = chart.data.datasets[0].data;
                                                                const total = data.reduce((a, b) => a + b, 0);

                                                                return defaultLabels.map((label, i) => {
                                                                    const value = data[i];
                                                                    const percentage = ((value / total) * 100).toFixed(1);
                                                                    return {
                                                                        ...label,
                                                                        text: `${label.text} – ${percentage}%`,
                                                                    };
                                                                });
                                                            }
                                                        }
                                                    },
                                                    tooltip: {
                                                        bodyFont: {
                                                            family: 'Inter',
                                                            size: 14
                                                        },
                                                        callbacks: {
                                                            label: function(context) {
                                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                                const value = context.parsed;
                                                                const percentage = ((value / total) * 100).toFixed(1);
                                                                return `${context.label}: ${value} (${percentage}%)`;
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                        });
                                    }
                                })();
                            </script>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endforeach
