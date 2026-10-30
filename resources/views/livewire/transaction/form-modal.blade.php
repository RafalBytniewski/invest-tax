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
                <div
                    x-data="{
                        highlightedIndex: -1,
                        showAssetResults: true,
                        moveHighlight(direction) {
                            const options = this.$refs.assetResults?.querySelectorAll('[data-asset-option]') ?? [];

                            if (options.length === 0) {
                                return;
                            }

                            this.highlightedIndex = (this.highlightedIndex + direction + options.length) % options.length;
                            options[this.highlightedIndex]?.scrollIntoView({ block: 'nearest' });
                        },
                        selectHighlighted() {
                            const options = this.$refs.assetResults?.querySelectorAll('[data-asset-option]') ?? [];

                            if (this.highlightedIndex >= 0 && options[this.highlightedIndex]) {
                                options[this.highlightedIndex].click();
                            }
                        }
                    }"
                    class="relative mb-1"
                >
                    <label for="transaction-asset" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Asset</label>

                    @if ($asset === null)
                        <div class="relative">
                            <input
                                id="transaction-asset"
                                type="search"
                                wire:model.live.debounce.300ms="assetSearch"
                                x-on:focus="showAssetResults = true"
                                x-on:keydown.arrow-down.prevent="moveHighlight(1)"
                                x-on:keydown.arrow-up.prevent="moveHighlight(-1)"
                                x-on:keydown.enter.prevent="selectHighlighted()"
                                x-on:keydown.escape.prevent="showAssetResults = false; highlightedIndex = -1"
                                x-on:keydown.home.prevent="highlightedIndex = 0"
                                x-on:keydown.end.prevent="highlightedIndex = (($refs.assetResults?.querySelectorAll('[data-asset-option]').length ?? 1) - 1)"
                                autocomplete="off"
                                role="combobox"
                                aria-autocomplete="list"
                                aria-controls="transaction-asset-results"
                                :aria-expanded="showAssetResults"
                                placeholder="Search by symbol or name..."
                                class="h-14 w-full rounded-lg border border-gray-300 bg-white px-4 text-base text-gray-900 shadow-sm focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700"
                            >

                            @if (mb_strlen(trim($assetSearch)) >= 2)
                                <div
                                    id="transaction-asset-results"
                                    x-ref="assetResults"
                                    x-show="showAssetResults"
                                    x-cloak
                                    role="listbox"
                                    class="absolute inset-x-0 top-full z-20 mt-2 max-h-80 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
                                >
                                    @if ($assetResultsByType->isNotEmpty())
                                        <div>
                                            @php $assetOptionIndex = 0; @endphp
                                            @foreach ($assetResultsByType as $assetType => $assetTypeResults)
                                                <section class="border-b border-gray-200 last:border-b-0 dark:border-zinc-800">
                                                    <div class="flex items-center gap-2 bg-gray-50 px-4 py-2.5 dark:bg-zinc-950/70">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400 dark:bg-zinc-500"></span>
                                                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-gray-600 dark:text-zinc-300">
                                                            {{ $assetTypeLabels[$assetType] }}
                                                        </p>
                                                        <span class="text-xs text-gray-400 dark:text-zinc-500">{{ $assetTypeResults->count() }}</span>
                                                    </div>

                                                    <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                                                        @foreach ($assetTypeResults as $assetResult)
                                                            <button
                                                                type="button"
                                                                data-asset-option
                                                                role="option"
                                                                wire:key="transaction-asset-result-{{ $assetResult->id }}"
                                                                wire:click="selectAsset({{ $assetResult->id }})"
                                                                x-on:mouseenter="highlightedIndex = {{ $assetOptionIndex }}"
                                                                x-on:click="showAssetResults = false"
                                                                x-bind:class="highlightedIndex === {{ $assetOptionIndex }} ? 'bg-gray-100 dark:bg-zinc-800' : ''"
                                                                class="flex w-full items-center justify-between gap-4 px-4 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-zinc-800/70"
                                                            >
                                                                <span class="min-w-0">
                                                                    <span class="block truncate text-sm font-semibold text-gray-900 dark:text-white">
                                                                        <span class="mr-2 text-gray-600 dark:text-zinc-300">{{ $assetResult->symbol }}</span>{{ $assetResult->name }}
                                                                    </span>
                                                                </span>
                                                                <span
                                                                    class="shrink-0 text-xs font-semibold uppercase text-gray-500 dark:text-zinc-400"
                                                                    title="{{ $assetResult->exchange?->name ?? 'No exchange' }}"
                                                                    aria-label="Exchange: {{ $assetResult->exchange?->name ?? 'No exchange' }}"
                                                                >
                                                                    {{ $assetResult->exchange?->symbol ?? '—' }}
                                                                </span>
                                                            </button>
                                                            @php $assetOptionIndex++; @endphp
                                                        @endforeach
                                                    </div>
                                                </section>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="px-4 py-4 text-sm text-gray-500 dark:text-zinc-400">No assets found for “{{ $assetSearch }}”.</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex min-h-14 items-center justify-between gap-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-900/70 dark:bg-emerald-950/30">
                            <div class="min-w-0">
                                <p class="truncate text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $selectedAsset?->symbol }} · {{ $selectedAsset?->name }}
                                </p>
                                <p class="mt-1 text-xs text-gray-600 dark:text-zinc-300">
                                    {{ $assetTypeLabels[$selectedAsset?->asset_type] ?? $selectedAsset?->asset_type }}
                                    · {{ $selectedAsset?->exchange?->name ?? 'No exchange' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                wire:click="clearAssetSelection"
                                class="shrink-0 rounded-lg border border-emerald-300 px-3 py-2 text-xs font-semibold text-emerald-800 transition hover:bg-emerald-100 dark:border-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-950/60"
                            >
                                Clear
                            </button>
                        </div>
                    @endif

                    @error('asset') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                </div>

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
                        <label for="transaction-quantity" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Quantity</label>
                        <input id="transaction-quantity" type="number" step="0.00000001" min="0" wire:model.live="quantity" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                        @error('quantity') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="transaction-price" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Price per unit</label>
                        <div class="relative">
                            <input id="transaction-price" type="number" step="0.00000001" min="0" wire:model.live="price_per_unit" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 pr-16 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-gray-500 dark:text-zinc-400">{{ $priceCurrency ?? '—' }}</span>
                        </div>
                        @error('price_per_unit') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    @if ($priceCurrency && $walletCurrency && $priceCurrency !== $walletCurrency)
                        <div>
                            <label for="transaction-exchange-rate" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Exchange rate</label>
                            <div class="relative">
                                <input id="transaction-exchange-rate" type="number" step="0.00000001" min="0" wire:model.live="exchange_rate" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 pr-24 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-gray-500 dark:text-zinc-400">{{ $walletCurrency }}/{{ $priceCurrency }}</span>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-zinc-400">1 {{ $priceCurrency }} = {{ $exchange_rate ?: '—' }} {{ $walletCurrency }}</p>
                            @error('exchange_rate') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div>
                        <label for="transaction-total" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Total value</label>
                        <div class="relative">
                            <input id="transaction-total" type="number" step="0.00000001" min="0" wire:model.live="total_value" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 pr-16 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-gray-500 dark:text-zinc-400">{{ $walletCurrency ?? '—' }}</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-zinc-400">Enter a total to calculate the exchange rate, or edit the rate manually.</p>
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

                <div class="rounded-lg border border-gray-200 dark:border-zinc-800">
                    <div class="border-b border-gray-200 px-4 py-3 dark:border-zinc-800">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-zinc-100">Attachments</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">Add purchase confirmations, fee receipts or statements. Files remain private.</p>
                    </div>

                    <div class="grid gap-4 p-4 sm:grid-cols-2">
                        <div>
                            <label for="transaction-attachment-type" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Document type</label>
                            <select id="transaction-attachment-type" wire:model="attachmentType" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-hidden focus:ring-2 focus:ring-gray-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700">
                                @foreach ($attachmentTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('attachmentType') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="transaction-attachments" class="mb-2 block text-sm font-medium text-gray-800 dark:text-zinc-200">Files</label>
                            <input id="transaction-attachments" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.csv" wire:model="attachmentFiles" class="block h-11 w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-3 file:h-full file:border-0 file:bg-gray-100 file:px-3 file:text-sm file:font-semibold dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-300 dark:file:bg-zinc-800">
                            <p class="mt-2 text-xs text-gray-500 dark:text-zinc-400">PDF, JPG, PNG or CSV, up to 10 MB per file.</p>
                            @error('attachmentFiles') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                            @error('attachmentFiles.*') <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if (count($attachmentFiles) > 0)
                        <div class="space-y-2 border-t border-gray-200 px-4 py-3 dark:border-zinc-800">
                            @foreach ($attachmentFiles as $file)
                                <p class="text-sm text-gray-700 dark:text-zinc-300">{{ $file->getClientOriginalName() }}</p>
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
