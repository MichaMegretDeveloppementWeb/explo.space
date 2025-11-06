@props([
    'label' => '',
    'value' => '',
    'type' => 'text', // text | badge | link | custom
])

<div class="grid grid-cols-3 gap-4 py-3 border-b border-gray-100 last:border-0">
    <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
    <dd class="text-sm text-gray-900 col-span-2">
        @if($type === 'badge')
            {{ $slot }}
        @elseif($type === 'link')
            <a href="{{ $value }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                {{ $slot->isEmpty() ? $value : $slot }}
            </a>
        @elseif($type === 'custom')
            {{ $slot }}
        @else
            {{ $value ?: '—' }}
        @endif
    </dd>
</div>
