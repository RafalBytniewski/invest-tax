<div
    x-data="{ open: @entangle('showModal') }"
    x-show="open"
    x-cloak
    x-transition.opacity
    x-on:keydown.escape.window="$wire.closeModal()"
    @click.self="$wire.closeModal()"
    class="fixed inset-0 z-50 flex items-end justify-center bg-black/60 p-2 sm:items-center sm:p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="new-transaction-title"
>
    <div class="w-full max-w-5xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 dark:border-zinc-700">
            <div>
                <h2 id="new-transaction-title" class="text-base font-semibold text-gray-900 sm:text-lg dark:text-zinc-100">Add New Transaction</h2>
                <p class="text-xs text-gray-500 dark:text-zinc-400">Record a buy or sell operation for your portfolio.</p>
            </div>
            <button
                type="button"
                wire:click="closeModal"
                class="rounded-lg border border-gray-300 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
            >
                Close
            </button>
        </div>

        <form wire:submit.prevent="save" class="relative">
            <div class="max-h-[80vh] space-y-4 overflow-y-auto p-3 sm:p-4">
                @if ($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-zinc-200">Transaction Context</h3>
                    <div class="grid gap-3 md:grid-cols-3">
                        <div class="space-y-1">
                            <label for="wallet" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Wallet</label>
                            <select
                                id="wallet"
                                wire:model.live="wallet"
                                required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            >
                                <option value="">Select wallet</option>
                                @foreach ($wallets as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('wallet') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="asset" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Asset</label>
                            <select
                                id="asset"
                                wire:model.live="asset"
                                required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            >
                                <option value="">Select asset</option>
                                @foreach ($assets as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('asset') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="type" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Type</label>
                            <select
                                id="type"
                                wire:model.live="type"
                                required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            >
                                <option value="">Select type</option>
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-zinc-200">Transaction Details</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="space-y-1">
                            <label for="currency" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Currency</label>
                            <input
                                id="currency"
                                type="text"
                                wire:model.live="currency"
                                maxlength="3"
                                required
                                placeholder="USD"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 uppercase text-sm text-gray-900 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('currency') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="quantity" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Quantity</label>
                            <input
                                id="quantity"
                                type="number"
                                step="0.00000001"
                                min="0"
                                required
                                wire:model.live="quantity"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('quantity') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="price_per_unit" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Price per unit</label>
                            <input
                                id="price_per_unit"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                wire:model.live="price_per_unit"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('price_per_unit') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="total_fees" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Total fees</label>
                            <input
                                id="total_fees"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                wire:model.live="total_fees"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('total_fees') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="total_value" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Total value</label>
                            <input
                                id="total_value"
                                type="number"
                                step="0.01"
                                wire:model.live="total_value"
                                readonly
                                class="h-11 w-full rounded-lg border border-gray-200 bg-gray-100 px-3 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-200"
                            />
                            @error('total_value') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="date" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Date</label>
                            <input
                                id="date"
                                type="date"
                                wire:model.live="date"
                                required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('date') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-3 sm:p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-zinc-200">Notes</h3>
                    <div class="space-y-1">
                        <label for="notes" class="text-xs font-medium text-gray-600 dark:text-zinc-300">Optional</label>
                        <textarea
                            id="notes"
                            wire:model.live="notes"
                            rows="4"
                            maxlength="500"
                            placeholder="Write your notes..."
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 placeholder:text-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        ></textarea>
                        @error('notes') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </section>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-gray-200 bg-white px-3 py-3 sm:flex-row sm:justify-end sm:px-4 dark:border-zinc-700 dark:bg-zinc-900">
                <button
                    type="button"
                    wire:click="closeModal"
                    class="h-10 rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="h-10 rounded-lg border border-red-700 bg-red-700 px-4 text-sm font-semibold text-white transition hover:bg-red-600"
                >
                    Save Transaction
                </button>
            </div>
        </form>
    </div>
</div>
