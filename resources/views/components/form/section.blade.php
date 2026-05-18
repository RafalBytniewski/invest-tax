@props([
    'label',
    'columns' => 'sm:grid-cols-2',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800/60 md:p-5']) }}>
    <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.16em] text-slate-900 dark:text-slate-100">
        {{ $label }}
    </h2>

    <div class="{{ 'grid grid-cols-1 gap-4 ' . $columns }}">
        {{ $slot }}
    </div>
</div>
