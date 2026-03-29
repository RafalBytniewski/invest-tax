@props([
    'padding' => 'p-4 sm:p-6',
])

<section
    {{ $attributes->class("rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 {$padding}") }}>
    {{ $slot }}
</section>
