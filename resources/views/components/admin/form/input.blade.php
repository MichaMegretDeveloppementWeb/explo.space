@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'helperText' => '',
    'error' => '',
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

    <div class="relative">
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            placeholder="{{ $placeholder ?? "" }}"
            {{ $wire }}
            {{ $attributes->except(['class', 'label', 'name', 'type', 'required', 'placeholder', 'helperText', 'error', 'wire']) }}
            class="block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-900 placeholder:text-gray-400 transition-colors duration-150
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0
                   disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500
                   {{ $slot->isEmpty() ? 'px-3.5 py-2.5' : 'pl-3.5 pr-10 py-2.5' }}
                   {{ $error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
        >

        @if($slot->isNotEmpty())
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                {{ $slot }}
            </div>
        @endif
    </div>

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
