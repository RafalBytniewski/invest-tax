@props([
    'model',
    'label',
])

<div class="relative z-0 w-full mb-5 group">
    <label for="{{ $model }}" 
        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>

    <textarea
        wire:model="{{ $model }}"
        id="{{ $model }}"
        {{ $attributes
            ->merge([
                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                            focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
                            dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
            ])
            ->class([
                'border-red-500 focus:border-red-500 focus:ring-red-500' => $errors->has($model)
            ])
        }}
    ></textarea>

    @error($model)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
