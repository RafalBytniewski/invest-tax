@props([
    'model',
    'label',
    'required' => false,
    'options' => [],   // tablica ['value' => 'Label']
    'value' => null,
    'disabled' => false,
])

<div class="relative z-0 w-full mb-5 group">
    <label for="{{ $model }}"
        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>

    <select
        wire:model="{{ $model }}"
        id="{{ $model }}"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
               focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 
               dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 
               dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        @if ($required) required @endif
        @if ($disabled) disabled @endif
    >
        <option value=""></option>

        @foreach($options as $key => $optionLabel)
            <option value="{{ $key }}" @if($value == $key) selected @endif>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
</div>
