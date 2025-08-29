<div x-data="{ open: @entangle('showModal') }" x-show="open" x-transition
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">

    <div class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg p-6 shadow-lg w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <h1 class="block mb-4 text-2xl font-medium text-gray-900 dark:text-white">
            Dodaj nową transakcję
        </h1>
        <x-form>
            <x-form.section label="Transaction Context">
                <x-form.select name="wallet" label="Wallet" required />
                <x-form.select name="asset" label="Asset" required />
                <x-form.select name="type" label="Type" required />

            </x-form>
            <x-form.section label="Transaction Details">
                <x-form.input name="currency" label="Currency" placeholder="PLN, USD, EUR, GBP" required />
                <x-form.input name="quantity" label="Quantity" required />
                <x-form.input name="price_per_unit" label="Price per unit" required />
                <x-form.input name="total_fees" label="Total fees" value="0" required />
                <x-form.input type="date" name="date" label="Date" required />
            </x-form.section>
            <x-form.section class="flex flex-col" label="Optional">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes"></textarea>
            </x-form.section>

            <div class="mt-6 flex justify-end gap-2">
                <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                    Save
                </button>
                <button wire:click="closeModal" class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 border-b-4 border-blue-700 hover:border-blue-500 rounded">
                    Cancel
                </button>
            </div>
        </x-form>
    </div>

</div>
