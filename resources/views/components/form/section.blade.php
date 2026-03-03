@props(['label'])

<div class="bg-white dark:bg-gray-600 rounded-lg  border-gray-200 dark:border-gray-900 p-4 md:p-6 m-2">
    <h2 class="text-lg font-semibold mb-4">{{ $label }}</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
        {{ $slot }}
    </div>
</div>