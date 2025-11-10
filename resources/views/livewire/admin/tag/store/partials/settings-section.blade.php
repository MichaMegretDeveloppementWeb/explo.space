{{-- Color picker --}}
<div class="space-y-2">
    <span class="block text-sm font-medium text-gray-700">
        Couleur
    </span>

    {{-- Hidden native color input --}}
    <input
        type="color"
        id="color"
        wire:model.live="color"
        class="sr-only">

    {{-- Custom color picker trigger --}}
    <button
        type="button"
        onclick="document.getElementById('color').click()"
        class="inline-flex items-center gap-3 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all cursor-pointer group">
        <div class="w-10 h-10 rounded-md border-2 border-gray-300 shadow-sm group-hover:border-gray-400 transition-colors" style="background-color: {{ $color }}"></div>
        <div class="flex flex-col items-start">
            <code class="text-sm font-mono font-medium text-gray-900">{{ $color }}</code>
            <span class="text-xs text-gray-500">Cliquez pour modifier</span>
        </div>
    </button>

    @error('color')
        <p class="mt-2 text-sm text-red-600 error-message">{{ $message }}</p>
    @enderror
</div>

{{-- Active toggle --}}
<div class="space-y-2 mt-12">
    <label class="flex items-start cursor-pointer group w-max">
        <div class="flex items-center h-6 relative">
            <input
                type="checkbox"
                wire:model.live="is_active"
                class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:bg-blue-600 transition-colors duration-200"></div>
            <div class="absolute top-[2px] start-[2px] bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform duration-200 peer-checked:translate-x-full peer-checked:border-white"></div>
        </div>
        <div class="ml-3">
            <span class="text-sm font-medium text-gray-900">Tag actif</span>
            <p class="text-sm text-gray-500">
                Un tag inactif ne sera pas affiché aux visiteurs
            </p>
        </div>
    </label>
    @error('is_active')
        <p class="mt-2 text-sm text-red-600 error-message">{{ $message }}</p>
    @enderror
</div>
