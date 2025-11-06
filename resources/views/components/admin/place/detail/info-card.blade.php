@props([
    'title' => '',
    'icon' => null,
    'count' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden']) }}>
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                @if($icon)
                    <x-dynamic-component :component="$icon" class="h-5 w-5 text-gray-500" />
                @endif
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            </div>
            @if($count !== null)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $count }}
                </span>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</div>
