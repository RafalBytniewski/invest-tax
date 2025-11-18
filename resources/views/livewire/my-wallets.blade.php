<div>
    @foreach ($wallets as $wallet)
        <div class="relative aspect-video p-5 my-5 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />

            <div>
                <span class="text-2xl text-gray-900 dark:text-white">{{ $wallet->name }}:</span>
                <span class="text-xl text-gray-900 dark:text-white">Transactions:
                    {{ $wallet->transactions->count() }}</span>
                <span class="text-xl text-gray-900 dark:text-white">Wallet value:
                    {{ $wallet->transactions->sum('total_value') }}</span>
                @php
                    $walletTotal = $wallet->transactions->sum('total_value');
                @endphp

                <ul>
                    @foreach ($wallet->transactions->groupBy('asset_id') as $assetId => $transactions)
                        @php
                            $assetName = $transactions->first()->asset->name; // nazwa assetu
                            $assetTotal = $transactions->sum('total_value'); // suma wartości tego assetu
                            $percentage = $walletTotal > 0 ? round(($assetTotal / $walletTotal) * 100, 2) : 0;
                            $quantity = $transactions->sum('quantity');
                        @endphp
                        <li>
                            <b>{{ $assetName }}</b> — Quantity = {{ $quantity }} — {{ $percentage }} %
                        </li>
                    @endforeach
                </ul>



            </div>
            <div>
                @if ($wallet->transactions->isNotEmpty())
                    <table class="table-auto border-1">
                        <thead class="border-1">
                            <tr>
                                <th class="p-2 text-md text-gray-800 dark:text-white">Asset</th>
                                <th class="p-2 text-md text-gray-800 dark:text-white">Type</th>
                                <th class="p-2 text-md text-gray-800 dark:text-white">Quantity</th>
                                <th class="p-2 text-md text-gray-800 dark:text-white">Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wallet->transactions as $transaction)
                                <tr>
                                    <td class="p-1 text-sm text-gray-800 dark:text-white">
                                        {{ $transaction->asset->symbol }}</td>
                                    <td class="p-1 text-sm text-gray-800 dark:text-white">{{ $transaction->type }}</td>
                                    <td class="p-1 text-sm text-gray-800 dark:text-white">{{ $transaction->quantity }}
                                    </td>
                                    <td class="p-1 text-sm text-gray-800 dark:text-white">
                                        {{ number_format($transaction->total_value, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No transactions.</p>
                @endif
            </div>
        </div>
    @endforeach
</div>
