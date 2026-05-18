@props([
    'type' => 'text',
    'model',
    'label',
    'wireModifier' => null,
    'prefix' => null,
    'suffix' => null,
])

<div class="w-full">
    <label for="{{ $model }}"
        class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">
        {{ $label }}
    </label>

    <div
        class="flex min-h-12 items-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50/80 transition focus-within:border-slate-400 focus-within:bg-white focus-within:ring-2 focus-within:ring-slate-200 dark:border-slate-700 dark:bg-slate-900/70 dark:focus-within:border-slate-500 dark:focus-within:bg-slate-900 dark:focus-within:ring-slate-800">
        @if($prefix)
            <span
                class="flex h-full items-center border-r border-slate-200 bg-white px-3 text-sm font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $prefix }}
            </span>
        @endif

        <input
            type="{{ $type }}"
            wire:model{{ $wireModifier ? ".$wireModifier" : '' }}="{{ $model }}"
            id="{{ $model }}"
            {{ $attributes
                ->merge([
                    'class' => 'h-12 w-full flex-1 border-0 bg-transparent px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 dark:text-slate-100 dark:placeholder:text-slate-500'
                ])
                ->class([
                    'text-rose-700 dark:text-rose-300' => $errors->has($model)
                ])
            }}
        />

        @if($suffix)
            <span
                class="mr-2 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $suffix }}
            </span>
        @endif
    </div>

    @error($model)
        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
