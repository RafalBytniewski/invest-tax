@props([
    'model',
    'label',
    'options' => [],
    'value' => null,
])

<div class="w-full">
    <label for="{{ $model }}"
        class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">
        {{ $label }}
    </label>

    <select
        wire:model="{{ $model }}"
        id="{{ $model }}"
        {{ $attributes
            ->merge([
                'class' => 'block h-12 w-full rounded-xl border border-slate-200 bg-slate-50/80 px-4 text-sm text-slate-900 transition hover:bg-white focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-100 dark:hover:bg-slate-900 dark:focus:border-slate-500 dark:focus:bg-slate-900 dark:focus:ring-slate-800'
            ])
            ->class([
                'border-rose-300 text-rose-700 focus:border-rose-400 focus:ring-rose-100 dark:border-rose-800 dark:text-rose-300 dark:focus:ring-rose-950' => $errors->has($model)
            ])
        }}
    >
        <option value=""></option>
        @foreach($options as $key => $optionLabel)
            <option value="{{ $key }}" @if($value == $key) selected @endif>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($model)
        <p class="mt-2 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
    @enderror
</div>
