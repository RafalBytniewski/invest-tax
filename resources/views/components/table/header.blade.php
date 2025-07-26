@props([
    'sortable' => null,
    'direction' => null
])

<th {{ $attributes->merge(['class' => 'px-6 py-3 bg-gray-50 dark:bg-gray-800'])->only('class') }}>
    @unless($sortable)
        <span class="text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            {{ $slot }}
        </span>
    @else
        <button {{ $attributes->except('class') }} class="flex items-center space-x-1 text-left text-s leading-4 font-medium text-gray-900 dark:text-gray-100">
            <span>{{ $slot }}</span>

            <span>
                @if($direction === 'asc')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                @elseif($direction === 'desc')
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                @endif
            </span>
        </button>
    @endif
</th>
