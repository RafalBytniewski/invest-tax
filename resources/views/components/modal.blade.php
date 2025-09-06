<div x-data="{ open: @entangle('showModal') }" x-show="open" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div
        class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg p-6 shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h1 class="block mb-4 text-2xl font-medium text-gray-900 dark:text-white">
            Add new transaction
        </h1>
        <x-form wire:submit.prevent="save">
            <x-form.section label="Transaction Context">
                <x-form.select model="wallet" label="Wallet" :options="$wallets" required />
                <x-form.select model="asset" label="Asset/Exchange" :options="$assets" required />
                <x-form.select model="type" label="Type" :options="$types" required />
            </x-form.section>
            <x-form.section label="Transaction Details">
                <x-form.input model="currency" label="Currency" placeholder="PLN, USD, EUR, GBP" required />

                <x-form.input type="number" model="quantity" wireModifier="live" label="Quantity" required />
                <x-form.input type="number" model="price_per_unit" wireModifier="live" label="Price per unit"
                    required />
                <x-form.input type="number" model="total_fees" wireModifier="live" label="Total fees" value="0"
                    required />
                <x-form.input type="number" model="total_value" label="Total value" value="0" readonly disabled />
                <x-form.input type="date" model="date" label="Date" required />
            </x-form.section>
            <x-form.section label="Optional">
                <div class="flex flex-col col-span-2">
                    <x-form.textarea model="notes" label="Notes" placeholder="Write your notes..."></x-form.textarea>
            </x-form.section>

            <div class="mt-6 flex justify-end gap-2">
                <x-form.button type="submit"
                    class="bg-transparent hover:bg-blue-500 text-blue-600 hover:text-white border-blue-500 hover:border-transparent">
                    Save
                </x-form.button>
                <x-form.button type="button" wire:click="closeModal"
                    class="bg-blue-500 text-white border-blue-500 hover:border-blue-500 hover:text-blue-700 hover:bg-blue-200">
                    Cancel
                </x-form.button>
            </div>
        </x-form>
    </div>
</div>
