@props([
    'status' => 'draft', // draft | published
    'locale' => null,
])

@php
    $colors = [
        'draft' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'published' => 'bg-green-100 text-green-800 border-green-200',
    ];

    $labels = [
        'draft' => 'Brouillon',
        'published' => 'Publié',
    ];

    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$colorClass}"]) }}>
    @if($locale)
        <span class="uppercase font-semibold mr-1">{{ $locale }}</span>
        <span class="mx-1">•</span>
    @endif
    {{ $label }}
</span>
