<x-layouts.app :title="__('Dashboard')">
    @php
        $userId = auth()->id();
        $walletCount = \App\Models\Wallet::where('user_id', $userId)->count();
        $assetCount = \App\Models\Transaction::where('user_id', $userId)->distinct('asset_id')->count('asset_id');
        $transactionCount = \App\Models\Transaction::where('user_id', $userId)->count();
    @endphp

    <div class="mx-auto w-full max-w-7xl space-y-4 p-4 sm:p-5">
        <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-200 text-sm font-semibold text-black dark:bg-neutral-700 dark:text-white">
                    {{ auth()->user()->initials() }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-gray-500 dark:text-zinc-400">{{ auth()->user()->email }}</p>
                </div>
                <span class="ms-auto rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-600/40 dark:bg-emerald-900/30 dark:text-emerald-300">
                    Logged in
                </span>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-xl border border-gray-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-zinc-400">Wallets</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ number_format($walletCount) }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-zinc-400">Assets</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ number_format($assetCount) }}</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-zinc-400">Transactions</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ number_format($transactionCount) }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 sm:p-5">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-zinc-100">Quick actions</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                <a
                    href="{{ route('assets') }}"
                    wire:navigate
                    class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                >
                    View Assets
                </a>
                <a
                    href="{{ route('my-transactions') }}"
                    wire:navigate
                    class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                >
                    View Transactions
                </a>
            </div>
        </section>
    </div>
</x-layouts.app>
