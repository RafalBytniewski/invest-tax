<div>
    <div
        x-data="{ open: @entangle('showModal') }"
        x-show="open"
        x-cloak
        x-on:keydown.escape.window="$wire.closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-gray-900/50" wire:click="closeModal"></div>

        <div
            x-show="open"
            x-transition.scale.origin.center
            @click.stop
            class="relative max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl dark:border-zinc-800 dark:bg-zinc-900"
        >
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-zinc-800 sm:px-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-zinc-400">
                        Activity
                    </p>
                    <h2 class="mt-1 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $mode === 'new' ? 'New transaction' : 'Edit transaction' }}
                    </h2>
                </div>

                <button
                    type="button"
                    wire:click="closeModal"
                    aria-label="Close"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-5 p-5 sm:p-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="transaction-wallet" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Wallet</label>
                        <select id="transaction-wallet" wire:model="wallet" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                            <option value="">Select wallet</option>
                            @foreach ($wallets as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('wallet') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-asset" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Asset</label>
                        <select id="transaction-asset" wire:model="asset" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                            <option value="">Select asset</option>
                            @foreach ($assets as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('asset') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-type" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Type</label>
                        <select id="transaction-type" wire:model="type" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                            <option value="">Select type</option>
                            @foreach ($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-currency" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Currency</label>
                        <select id="transaction-currency" wire:model="currency" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                            <option value="">Select currency</option>
                            @foreach ($currencies as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('currency') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-quantity" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Quantity</label>
                        <input id="transaction-quantity" type="number" step="0.00000001" min="0" wire:model.live="quantity" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                        @error('quantity') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-price" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Price per unit</label>
                        <input id="transaction-price" type="number" step="0.01" min="0" wire:model.live="price_per_unit" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                        @error('price_per_unit') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-total" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Total value</label>
                        <input id="transaction-total" type="number" step="0.01" min="0" wire:model="total_value" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                        @error('total_value') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-date" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Date</label>
                        <input id="transaction-date" type="date" wire:model="date" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                        @error('date') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-4 py-3 dark:border-zinc-800">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-zinc-100">Transaction fees</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Optional fees saved separately from the transaction value.</p>
                        </div>
                        <button type="button" wire:click="addFee" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Add fee</button>
                    </div>

                    @if (count($fees) > 0)
                        <div class="space-y-4 p-4">
                            @foreach ($fees as $index => $fee)
                                <div class="grid gap-4 sm:grid-cols-[1fr_0.7fr_0.7fr_auto]">
                                    <div>
                                        <label for="transaction-fee-type-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Type</label>
                                        <select id="transaction-fee-type-{{ $index }}" wire:model="fees.{{ $index }}.type" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                                            @foreach ($feeTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error("fees.{$index}.type") <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="transaction-fee-currency-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Currency</label>
                                        <select id="transaction-fee-currency-{{ $index }}" wire:model="fees.{{ $index }}.currency" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                                            @foreach ($currencies as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error("fees.{$index}.currency") <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="transaction-fee-value-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Value</label>
                                        <input id="transaction-fee-value-{{ $index }}" type="number" step="0.01" min="0" wire:model="fees.{{ $index }}.value" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                                        @error("fees.{$index}.value") <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex items-end">
                                        <button type="button" wire:click="removeFee({{ $index }})" class="h-11 w-full rounded-lg border border-rose-200 px-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-400 dark:hover:bg-rose-950/30" aria-label="Remove fee">Remove</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <label for="transaction-notes" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Notes</label>
                    <textarea id="transaction-notes" wire:model="notes" maxlength="500" rows="3" placeholder="Optional notes" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-600 dark:focus:border-zinc-500 dark:focus:ring-zinc-700"></textarea>
                    @error('notes') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-zinc-800">
                    <button type="button" wire:click="closeModal" class="h-10 rounded-lg border border-gray-300 px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" class="h-10 rounded-lg bg-gray-900 px-4 text-sm font-semibold text-white transition hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300">Save transaction</button>
                </div>
            </form>
        </div>
    </div>


</div>
