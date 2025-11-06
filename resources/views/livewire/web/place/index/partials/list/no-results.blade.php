{{-- Empty State 2 : Recherche effectuée SANS résultats --}}
{{-- Style : Gris neutre, suggestion d'ajustement --}}
{{-- Variables attendues : $title, $message, $suggestion --}}

<div class="p-8 text-center">
    {{-- Icône grise neutre (loupe) --}}
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    {{-- Titre neutre --}}
    <h3 class="text-base font-medium text-gray-900 mb-2">
        {{ $title }}
    </h3>

    {{-- Message principal avec max-width pour lisibilité --}}
    <p class="text-sm text-gray-500 max-w-sm mx-auto">
        {{ $message }}
    </p>
</div>
