<div>
    <form wire:submit="updateProfile">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                <p class="mt-1 text-sm text-gray-600">Modifiez votre nom et votre adresse email</p>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom complet
                </label>
                <input
                    type="text"
                    wire:model="name"
                    id="name"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors @error('name') border-red-500 @enderror"
                    placeholder="Votre nom complet"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse email
                </label>
                <input
                    type="email"
                    wire:model="email"
                    id="email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors @error('email') border-red-500 @enderror"
                    placeholder="votre@email.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if($email !== auth()->user()->email)
                    <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-amber-500" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">Vérification requise</h3>
                                <p class="mt-1 text-sm text-amber-700">
                                    Si vous modifiez votre email, vous devrez le vérifier avant de pouvoir vous reconnecter.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submit button -->
            <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove>Enregistrer les modifications</span>
                    <span wire:loading.flex class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
