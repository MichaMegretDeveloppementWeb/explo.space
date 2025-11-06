{{-- Empty State 1 : Conditions minimales NON réunies --}}
{{-- Style : Bleu, encourageant, incitation à l'action --}}
{{-- Variables attendues : $title, $message --}}

<div class="p-8 text-center">
    {{-- Icône bleue encourageante (compass) --}}
    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
        </svg>
    </div>

    {{-- Titre encourageant --}}
    <h3 class="text-lg font-semibold text-gray-900 mb-2">
        {{ $title }}
    </h3>

    {{-- Message directif avec max-width pour lisibilité --}}
    <p class="text-sm text-gray-600 max-w-sm mx-auto">
        {{ $message }}
    </p>
</div>
