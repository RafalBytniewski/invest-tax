<div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak x-transition.opacity.duration.200ms
    x-on:keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        @click="open = false"></div>

    <div x-show="open" x-transition.scale.origin.center.duration.200ms @click.stop
            class="relative w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-800">
        <div
            class="flex items-center justify-between border-b border-slate-200 bg-white/95 px-6 py-4 dark:border-slate-700 dark:bg-slate-800/95">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Manage Funds</h1>
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    {{ $walletName }} {{ $walletCurrency ? '(' . $walletCurrency . ')' : '' }}
                </p>
            </div>
            <button type="button" wire:click="closeModal"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100"
                aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <x-form wire:submit.prevent="save" class="max-w-none p-4 md:p-6">
            <div class="grid grid-cols-1 gap-4">
                <x-form.section label="Ledger Entry" columns="sm:grid-cols-2">
                    <x-form.select model="source" label="Action" :options="[
                        'manual_deposit' => 'Deposit',
                        'manual_withdrawal' => 'Withdraw',
                    ]" required />
                    <x-form.input type="number" model="amount" label="Amount" step="0.01" required />
                    <x-form.input type="date" model="date" label="Date" required />
                </x-form.section>

                <x-form.section label="Notes" columns="grid-cols-1">
                    <x-form.textarea model="notes" label="Notes" placeholder="Optional note..." maxlength="500" />
                </x-form.section>
            </div>

            <div class="mt-4 flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end dark:border-slate-700">
                <x-form.button type="button" wire:click="closeModal"
                    class="w-full border-slate-300 bg-white text-slate-700 hover:bg-slate-100 sm:w-auto dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">
                    Cancel
                </x-form.button>
                <x-form.button type="submit"
                    class="w-full border-emerald-700 bg-emerald-700 text-white hover:border-emerald-600 hover:bg-emerald-600 sm:w-auto">
                    Save
                </x-form.button>
            </div>
        </x-form>
    </div>
</div>
