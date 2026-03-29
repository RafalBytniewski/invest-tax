<x-layouts.app :title="__('Dashboard')">
    <x-page-shell>
        <x-page-section>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span
                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                        Dashboard
                    </span>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Overview of your investment workspace
                    </h1>
                    <p class="mt-2 max-w-3xl text-sm text-gray-500 dark:text-zinc-400">
                        This page now follows the same layout proportions and card system as the rest of the application.
                    </p>
                </div>
            </div>
        </x-page-section>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-page-section class="relative overflow-hidden">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
                <div class="relative">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Recent Activity</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">Last Transactions</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Reserved space for recent operations summary.</p>
                </div>
            </x-page-section>

            <x-page-section class="relative overflow-hidden">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
                <div class="relative">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Allocation</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">Asset Breakdown</p>
                    <div class="mt-4 space-y-2 text-sm text-gray-600 dark:text-zinc-300">
                        <p>Crypto</p>
                        <p>Stocks</p>
                        <p>ETFs</p>
                    </div>
                </div>
            </x-page-section>

            <x-page-section class="relative overflow-hidden">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
                <div class="relative">
                    <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Performance</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">Snapshot</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">Reserved space for portfolio performance cards.</p>
                </div>
            </x-page-section>
        </div>

        <x-page-section class="relative min-h-[420px] overflow-hidden">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/10 dark:stroke-neutral-100/10" />
            <div class="relative max-w-2xl">
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">Workspace</p>
                <h2 class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">Main dashboard panel</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-zinc-400">
                    The container, spacing and card proportions are aligned with the assets and transactions pages.
                </p>
            </div>
        </x-page-section>
    </x-page-shell>
</x-layouts.app>
