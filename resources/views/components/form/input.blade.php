@props([
    'type' => 'text',
    'model',
    'label',
    'wireModifier' => null,
    'prefix' => null,
    'suffix' => null,
])

<div class="relative z-0 w-full mb-5 group">
    <label for="{{ $model }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>

    <div class="flex items-center border rounded-lg overflow-hidden 
                bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600
                focus-within:ring-2 focus-within:ring-blue-500">
        
        {{-- Prefix --}}
        @if($prefix)
            <span class="px-3 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800">
                {{ $prefix }}
            </span>
        @endif

        {{-- Input --}}
        <input 
            type="{{ $type }}"
            wire:model{{ $wireModifier ? ".$wireModifier" : '' }}="{{ $model }}"
            id="{{ $model }}"
            {{ $attributes
                ->merge([
                    'class' => 'flex-1 bg-transparent text-gray-900 dark:text-white text-sm border-0 px-3 py-2 
                                focus:ring-0 focus:outline-none'
                ])
                ->class([
                    'border-red-500 focus:border-red-500 focus:ring-red-500' => $errors->has($model)
                ])
            }}
        />

        {{-- Suffix --}}
        @if($suffix)
            <span class="px-1 text-gray-700 dark:text-gray-300 border-l border-gray-300 dark:border-gray-600">
                {{ $suffix }}
            </span>
        @endif
    </div>

    @error($model)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
