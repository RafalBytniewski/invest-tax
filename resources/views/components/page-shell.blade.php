@props([
    'width' => 'max-w-[1600px]',
    'gap' => 'space-y-6',
])

<div {{ $attributes->class("mx-auto w-full {$width} {$gap} px-4 py-4 sm:px-6 lg:px-8") }}>
    {{ $slot }}
</div>
