@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    {{-- Cercle extérieur --}}
    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" fill="none"/>

    {{-- Point central --}}
    <circle cx="12" cy="12" r="2" fill="currentColor"/>

    {{-- Lignes du viseur (haut, bas, gauche, droite) --}}
    <line x1="12" y1="1" x2="12" y2="5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <line x1="12" y1="19" x2="12" y2="23" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <line x1="1" y1="12" x2="5" y2="12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    <line x1="19" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
</svg>
