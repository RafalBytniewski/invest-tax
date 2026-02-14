<div class="space-y-6">
    @foreach ($wallets as $wallet)
        @php
            $summary = $walletSummaries[$wallet->id] ?? null;
            $assets = $summary['assets'] ?? [];
            $currency = strtoupper($wallet->currency ?? '');
            $currentValue = $summary['current_value'] ?? null;
            $coverage = $summary['current_value_coverage'] ?? 0;
        @endphp

        <section
            wire:key="wallet-card-{{ $wallet->id }}"
            class="overflow-hidden rounded-xl border border-neutral-700 bg-neutral-900 shadow-lg"
        >
            <div class="border-b border-neutral-700 px-4 py-4 sm:px-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-xl font-semibold text-white">{{ $wallet->name }}</h2>
                    <div class="grid grid-cols-2 gap-2 text-xs text-neutral-300 sm:flex sm:gap-4">
                        <span>Currency: <span class="font-semibold text-white">{{ $currency }}</span></span>
                        <span>Broker: <span class="font-semibold text-white">{{ $wallet->broker?->name ?? 'Unknown' }}</span></span>
                        <span>Created: <span class="font-semibold text-white">{{ $wallet->created_at->format('d.m.Y') }}</span></span>
                        <span>Owner: <span class="font-semibold text-white">-</span></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 border-b border-neutral-700 px-4 py-4 sm:grid-cols-3 lg:grid-cols-5 sm:px-6">
                <div class="rounded-lg border border-neutral-700 bg-neutral-800 p-3">
                    <p class="text-xs text-neutral-400">Assets</p>
                    <p class="text-lg font-semibold text-white">{{ $summary['asset_count'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-neutral-700 bg-neutral-800 p-3">
                    <p class="text-xs text-neutral-400">Transactions</p>
                    <p class="text-lg font-semibold text-white">{{ $summary['transaction_count'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-neutral-700 bg-neutral-800 p-3">
                    <p class="text-xs text-neutral-400">Invested</p>
                    <p class="text-lg font-semibold text-white">
                        {{ number_format($summary['invested_capital'] ?? 0, 2, '.', ' ') }}
                        <span class="text-xs">{{ $currency }}</span>
                    </p>
                </div>
                <div class="rounded-lg border border-neutral-700 bg-neutral-800 p-3">
                    <p class="text-xs text-neutral-400">Current Value</p>
                    <p class="text-lg font-semibold text-white">
                        @if ($currentValue === null)
                            -
                        @else
                            {{ number_format($currentValue, 2, '.', ' ') }}
                        @endif
                        <span class="text-xs">{{ $currency }}</span>
                    </p>
                    @if (($summary['asset_count'] ?? 0) > 0 && $coverage < 100)
                        <p class="text-[11px] text-amber-300">Loaded prices for {{ $coverage }}% of assets</p>
                    @endif
                </div>
                <div class="rounded-lg border border-neutral-700 bg-neutral-800 p-3">
                    <p class="text-xs text-neutral-400">Realized P/L</p>
                    @php $realized = $summary['realized_pl'] ?? 0; @endphp
                    <p class="text-lg font-semibold {{ $realized >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ number_format($realized, 2, '.', ' ') }}
                        <span class="text-xs">{{ $currency }}</span>
                    </p>
                </div>
            </div>

            <div class="grid gap-4 p-4 sm:p-6 xl:grid-cols-5">
                <div class="xl:col-span-3">
                    <div class="space-y-2">
                        @forelse ($assets as $asset)
                            <article class="rounded-lg border border-neutral-700 bg-neutral-800">
                                <div class="grid items-center gap-2 p-3 text-xs text-neutral-200 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <p class="font-semibold text-white" title="{{ $asset['name'] }}">
                                            {{ $asset['symbol'] }}{{ $asset['exchange_symbol'] ? '.' . $asset['exchange_symbol'] : '' }}
                                        </p>
                                        <p class="text-neutral-400">{{ $asset['name'] }}</p>
                                    </div>

                                    <div>
                                        <p class="text-neutral-400">Avg buy price</p>
                                        <p class="font-semibold text-white">
                                            {{ number_format($asset['avg_buy_price'], 2, '.', ' ') }}
                                            <span class="text-[11px]">{{ $currency }}</span>
                                        </p>
                                    </div>

                                    <div>
                                        <button
                                            wire:click="loadPrice('{{ $asset['symbol'] }}', '{{ $asset['exchange_symbol'] }}')"
                                            wire:loading.attr="disabled"
                                            wire:target="loadPrice"
                                            class="rounded border border-neutral-600 px-2 py-1 text-[11px] font-medium hover:bg-neutral-700 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            Refresh price
                                        </button>
                                        <p class="pt-1 font-semibold text-white">
                                            @if ($asset['current_value'] === null)
                                                -
                                            @else
                                                {{ number_format($asset['current_value'], 2, '.', ' ') }}
                                                <span class="text-[11px]">{{ $currency }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <button
                                            wire:click="toggleTransactions({{ $wallet->id }}, {{ $asset['id'] }})"
                                            aria-expanded="{{ $this->isTransactionsVisible($wallet->id, $asset['id']) ? 'true' : 'false' }}"
                                            class="flex items-center gap-1 rounded border border-neutral-600 px-2 py-1 text-[11px] font-medium hover:bg-neutral-700"
                                        >
                                            {{ $asset['transaction_count'] }} transactions
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if ($this->isTransactionsVisible($wallet->id, $asset['id']))
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                @endif
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                @if ($this->isTransactionsVisible($wallet->id, $asset['id']))
                                    <div class="overflow-x-auto border-t border-neutral-700">
                                        <table class="min-w-full text-xs">
                                            <thead class="bg-neutral-700 text-neutral-100">
                                                <tr>
                                                    <th class="px-3 py-2 text-left">Type</th>
                                                    <th class="px-3 py-2 text-left">Quantity</th>
                                                    <th class="px-3 py-2 text-left">Price per unit</th>
                                                    <th class="px-3 py-2 text-left">Total value</th>
                                                    <th class="px-3 py-2 text-left">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($asset['transactions'] as $transaction)
                                                    <tr class="border-t border-neutral-700 {{ $transaction->type === 'buy' ? 'bg-emerald-700/20' : ($transaction->type === 'sell' ? 'bg-rose-700/20' : '') }}">
                                                        <td class="px-3 py-2 capitalize">{{ $transaction->type }}</td>
                                                        <td class="px-3 py-2">{{ number_format($transaction->quantity, 8, '.', ' ') }}</td>
                                                        <td class="px-3 py-2">{{ number_format($transaction->price_per_unit, 2, '.', ' ') }}</td>
                                                        <td class="px-3 py-2">{{ number_format($transaction->total_value, 2, '.', ' ') }}</td>
                                                        <td class="px-3 py-2">{{ $transaction->date->format('d.m.Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="rounded-lg border border-dashed border-neutral-700 bg-neutral-800 p-6 text-center text-sm text-neutral-400">
                                No assets in this wallet yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="xl:col-span-2">
                    <div class="rounded-lg border border-neutral-700 bg-neutral-800 p-3">
                        <h3 class="border-b border-neutral-700 pb-2 text-lg font-semibold text-white">Allocation</h3>
                        <div wire:ignore class="pt-3">
                            <canvas id="wallet-chart-{{ $wallet->id }}" class="max-h-[360px]"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function () {
                    const labels = @json($summary['chart_labels'] ?? []);
                    const values = @json($summary['chart_values'] ?? []);
                    const canvas = document.getElementById('wallet-chart-{{ $wallet->id }}');

                    if (!canvas || !Array.isArray(values) || values.length === 0) {
                        return;
                    }

                    window.walletCharts = window.walletCharts || {};
                    const chartKey = 'wallet-{{ $wallet->id }}';

                    if (window.walletCharts[chartKey]) {
                        window.walletCharts[chartKey].destroy();
                    }

                    window.walletCharts[chartKey] = new Chart(canvas, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: values.map((v, i) => `hsla(${i * 45}, 65%, 60%, 0.8)`),
                                borderWidth: 1,
                                borderColor: '#262626'
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#ffffff',
                                        boxWidth: 14,
                                        padding: 12
                                    }
                                }
                            }
                        }
                    });
                })();
            </script>
        </section>
    @endforeach
</div>
