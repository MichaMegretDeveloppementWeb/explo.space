<div class="p-6">
    <div class="flex items-center">
        <div class="flex items-center h-5">
            <input type="checkbox"
                   wire:model="is_featured"
                   id="is_featured"
                   class="h-4 w-4 text-blue-600 focus:ring-2 focus:ring-blue-500 border-gray-300 rounded transition-colors">
        </div>
        <div class="ml-3">
            <label for="is_featured" class="text-sm font-medium text-gray-900 cursor-pointer">
                Mettre à l'affiche
            </label>
            <p class="text-xs text-gray-500 mt-0.5">
                Ce lieu sera affiché sur la page d'accueil
            </p>
        </div>
    </div>
</div>
