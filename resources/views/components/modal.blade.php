<div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak x-transition.opacity.duration.200ms
    x-on:keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        @click="open = false"></div>

    <div x-show="open" x-transition.scale.origin.center.duration.200ms @click.stop
        class="relative w-full max-w-5xl max-h-[92vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-800">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white/95 px-6 py-4 backdrop-blur dark:border-slate-700 dark:bg-slate-800/95">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">New Transaction</h1>
                <p class="text-sm text-slate-500 dark:text-slate-300">Add buy or sell operation to your portfolio.</p>
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
                <x-form.section label="Transaction Context" columns="sm:grid-cols-3">
                    <x-form.select model="wallet" label="Wallet" :options="$wallets" required />
                    <x-form.select model="asset" label="Asset" :options="$assets" required />
                    <x-form.select model="type" label="Type" :options="$types" required />
                </x-form.section>

                <x-form.section label="Transaction Details" columns="sm:grid-cols-2 lg:grid-cols-3">
                    <x-form.input model="currency" label="Currency" placeholder="PLN, USD, EUR, GBP" required />
                    <x-form.input type="number" model="quantity" wireModifier="lazy" label="Quantity" step="0.00000001"
                        required />
                    <x-form.input type="number" model="price_per_unit" wireModifier="lazy" label="Price per unit"
                        step="0.01" required />
                    <x-form.input type="number" model="total_fees" wireModifier="lazy" label="Total fees" value="0"
                        required />
                    <x-form.input type="number" model="total_value" label="Total value" step="0.01" value="0" />
                    <x-form.input type="date" model="date" label="Date" required />
                </x-form.section>
            </div>

            <x-form.section label="Notes" columns="grid-cols-1" class="mt-4">
                <div class="col-span-1">
                    <x-form.textarea model="notes" label="Notes" placeholder="Write your notes..." maxlength="500" />
                </div>
            </x-form.section>

            <div class="mt-4 flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end dark:border-slate-700">
                <x-form.button type="button" wire:click="closeModal"
                    class="w-full border-slate-300 bg-white text-slate-700 hover:bg-slate-100 sm:w-auto dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600">
                    Cancel
                </x-form.button>
                <x-form.button type="submit"
                    class="w-full border-emerald-700 bg-emerald-700 text-white hover:border-emerald-600 hover:bg-emerald-600 sm:w-auto">
                    Save Transaction
                </x-form.button>
            </div>
        </x-form>
    </div>
</div>
