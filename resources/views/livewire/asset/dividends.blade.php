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
