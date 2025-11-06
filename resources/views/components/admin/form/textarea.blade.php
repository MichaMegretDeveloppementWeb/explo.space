@props([
    'label' => '',
    'name' => '',
    'required' => false,
    'placeholder' => '',
    'helperText' => '',
    'error' => '',
    'rows' => 4,
    'wire' => '',
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-900 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $required ? 'required' : '' }}
        {{ $placeholder ? 'placeholder=' . $placeholder : '' }}
        {{ $wire }}
        {{ $attributes->except(['class', 'label', 'name', 'required', 'placeholder', 'helperText', 'error', 'rows', 'wire']) }}
        class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition-colors duration-150
               focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0
               disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500
               {{ $error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
    ></textarea>

    @if($helperText && !$error)
        <p class="mt-1.5 text-xs text-gray-500">{{ $helperText }}</p>
    @endif

    @if($error)
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span class="error-message">{{ $error }}</span>
        </p>
    @endif
</div>
