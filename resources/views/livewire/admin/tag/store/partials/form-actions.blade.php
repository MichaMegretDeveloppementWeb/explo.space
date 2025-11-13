{{-- Actions --}}
<div class="flex justify-end items-center gap-3 pt-2">
    <button type="submit"
            wire:loading.attr="disabled"
            wire:target="save"
            class="relative px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed">

        {{-- Texte normal (visible quand pas de chargement) --}}
        <span wire:loading.remove wire:target="save">
            {{ $mode === 'create' ? 'Créer le tag' : 'Enregistrer les modifications' }}
        </span>

        {{-- Loader + texte (visible pendant le chargement) --}}
        <span wire:loading.flex wire:target="save" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ $mode === 'create' ? 'Création en cours...' : 'Mise à jour en cours...' }}
        </span>
    </button>
</div>
