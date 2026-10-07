<div class="mx-auto w-full max-w-[1600px] space-y-6 px-4 sm:px-6 lg:px-8">
    <section
        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
        <div class="flex flex-col gap-1">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-600 dark:text-blue-400">
                Income
            </p>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                Dividends
            </h1>
            <p class="text-sm text-gray-500 dark:text-zinc-400">
                Full dividend history for {{ $asset->name }}.
            </p>
        </div>

        <div class="mt-5 overflow-x-auto">
            @if (count($dividendChartData) > 0)
                <div class="mb-6 rounded-lg border border-gray-200 p-4 dark:border-zinc-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Dividend history
                        </h2>
                        <div class="flex flex-wrap gap-2" aria-label="Chart timeframe">
                            <button type="button" data-dividend-chart-range="{{ $asset->id }}" data-years="3"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-blue-400 hover:text-blue-600 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-blue-500 dark:hover:text-blue-400">
                                3Y
                            </button>
                            <button type="button" data-dividend-chart-range="{{ $asset->id }}" data-years="10"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-blue-400 hover:text-blue-600 dark:border-zinc-700 dark:text-zinc-300 dark:hover:border-blue-500 dark:hover:text-blue-400">
                                10Y
                            </button>
                            <button type="button" data-dividend-chart-range="{{ $asset->id }}" data-years="all"
                                class="rounded-lg border border-blue-600 bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white transition">
                                ALL
                            </button>
                        </div>
                    </div>
                    <div class="relative h-72 w-full sm:h-96">
                        <canvas
                            id="dividend-chart-{{ $asset->id }}"
                            data-dividend-chart="{{ $asset->id }}"
                            data-dividend-currency="{{ $asset->asset_type === 'crypto' ? 'USD' : ($asset->exchange?->currency ?? '') }}"
                            data-chart-data='@json($dividendChartData)'
                            aria-label="Dividend amount history chart"
                            role="img"
                        ></canvas>
                    </div>
                </div>
            @endif

            <table class="min-w-full text-left text-sm">
                <thead
                    class="border-b border-gray-200 text-xs uppercase tracking-[0.12em] text-gray-500 dark:border-zinc-700 dark:text-zinc-400">
                    <tr>
                        <th scope="col" class="px-2 py-3 font-semibold">Ex-Date</th>
                        <th scope="col" class="px-2 py-3 font-semibold">Amount per Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse ($dividends as $dividend)
                        <tr>
                            <td class="whitespace-nowrap px-2 py-3 text-gray-700 dark:text-zinc-200">
                                {{ $dividend->ex_date->format('Y-m-d') }}
                            </td>
                            <td class="whitespace-nowrap px-2 py-3 tabular-nums text-gray-700 dark:text-zinc-200">
                                {{ rtrim(rtrim((string) $dividend->amount, '0'), '.') ?: '0' }}
                                <span class="ml-1 text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $asset->asset_type === 'crypto' ? 'USD' : ($asset->exchange?->currency ?? '') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-2 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">
                                No dividends found for this asset.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($dividends->hasPages())
            <div class="mt-5">
                {{ $dividends->links() }}
            </div>
        @endif
    </section>
</div>
