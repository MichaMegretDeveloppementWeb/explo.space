@props([
    'title',
    'value',
    'iconColor' => 'blue',
    'link' => null,
    'trend' => null,
    'trendUp' => true,
])

@php
// Couleurs pour les icônes avec gradients
$iconGradients = [
    'blue' => 'from-blue-500 to-blue-600',
    'green' => 'from-green-500 to-green-600',
    'purple' => 'from-purple-500 to-purple-600',
    'orange' => 'from-orange-500 to-orange-600',
    'red' => 'from-red-500 to-red-600',
    'indigo' => 'from-indigo-500 to-indigo-600',
    'pink' => 'from-pink-500 to-pink-600',
    'teal' => 'from-teal-500 to-teal-600',
];

// Couleurs de background claires pour les icônes
$iconBgColors = [
    'blue' => 'bg-blue-50',
    'green' => 'bg-green-50',
    'purple' => 'bg-purple-50',
    'orange' => 'bg-orange-50',
    'red' => 'bg-red-50',
    'indigo' => 'bg-indigo-50',
    'pink' => 'bg-pink-50',
    'teal' => 'bg-teal-50',
];

// Couleurs de texte pour les icônes
$iconTextColors = [
    'blue' => 'text-blue-600',
    'green' => 'text-green-600',
    'purple' => 'text-purple-600',
    'orange' => 'text-orange-600',
    'red' => 'text-red-600',
    'indigo' => 'text-indigo-600',
    'pink' => 'text-pink-600',
    'teal' => 'text-teal-600',
];

$iconGradient = $iconGradients[$iconColor] ?? $iconGradients['blue'];
$iconBg = $iconBgColors[$iconColor] ?? $iconBgColors['blue'];
$iconText = $iconTextColors[$iconColor] ?? $iconTextColors['blue'];
$isClickable = !empty($link);
@endphp

@if($isClickable)
<a href="{{ $link }}"
   {{ $attributes->merge([
    'class' => "group relative block bg-white rounded-xl border border-gray-200 p-6 transition-all duration-300 hover:shadow-sm hover:border-white hover:-translate-y-0.25"
]) }}>
@else
<div {{ $attributes->merge([
    'class' => "relative bg-white rounded-xl border border-gray-200 p-6 transition-all duration-200"
]) }}>
@endif
    <div class="flex items-center justify-start gap-2">
        {{-- Icône --}}
        <div class="flex-shrink-0 {{ $iconBg }} rounded-xl p-3">
            <div class="{{ $iconText }}">
                {{ $icon }}
            </div>
        </div>

        <p class="text-md font-normal text-gray-400">{{ $title }}</p>
    </div>

    {{-- Contenu --}}
    <div class="mt-4">
        <div class="flex items-baseline gap-2 px-3">
            <p class="text-3xl font-normal text-gray-900 tabular-nums" data-countup="{{ $value }}">
                {{ $value }}
            </p>
            @if($trend)
                <span class="inline-flex items-center gap-1 text-sm font-medium {{ $trendUp ? 'text-green-600' : 'text-red-600' }}">
                    @if($trendUp)
                        <x-heroicon-o-arrow-trending-up class="h-4 w-4" />
                    @else
                        <x-heroicon-o-arrow-trending-down class="h-4 w-4" />
                    @endif
                    <span>{{ $trend }}</span>
                </span>
            @endif
        </div>
    </div>
@if($isClickable)
</a>
@else
</div>
@endif

{{-- Script CountUp pour animation des chiffres --}}
@once
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation simple des chiffres au chargement
        const statElements = document.querySelectorAll('[data-countup]');

        statElements.forEach(el => {
            const target = parseInt(el.dataset.countup) || 0;
            const duration = 1000; // 1 seconde
            const steps = 30;
            const increment = target / steps;
            let current = 0;
            let step = 0;

            const timer = setInterval(() => {
                step++;
                current = Math.min(Math.ceil(increment * step), target);
                el.textContent = current;

                if (step >= steps || current >= target) {
                    el.textContent = target;
                    clearInterval(timer);
                }
            }, duration / steps);
        });
    });
</script>
@endonce
