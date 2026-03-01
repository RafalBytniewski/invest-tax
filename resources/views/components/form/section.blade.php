@props([
    'label',
    'columns' => 'md:grid-cols-2',
])

<section {{ $attributes->class('rounded-xl border border-slate-200 bg-slate-50/60 p-4 md:p-5 dark:border-slate-700 dark:bg-slate-900/30') }}>
    <h2 class="mb-3 text-base font-semibold text-slate-900 dark:text-slate-100">{{ $label }}</h2>

    <div class="grid grid-cols-1 {{ $columns }} gap-x-4 gap-y-2">
        {{ $slot }}
    </div>
</section>
