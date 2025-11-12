<div>
    <form wire:submit="updatePassword">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h3 class="text-lg font-medium text-gray-900">Modifier le mot de passe</h3>
                <p class="mt-1 text-sm text-gray-600">Assurez-vous d'utiliser un mot de passe fort et unique</p>
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Nouveau mot de passe
                </label>
                <input
                    type="password"
                    wire:model="password"
                    id="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors @error('password') border-red-500 @enderror"
                    placeholder="Entrez votre nouveau mot de passe"
                    autocomplete="new-password"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirmer le nouveau mot de passe
                </label>
                <input
                    type="password"
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                    placeholder="Confirmez votre nouveau mot de passe"
                    autocomplete="new-password"
                >
            </div>

            <!-- Security Tip -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-shield-check class="h-5 w-5 text-blue-500" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Conseil de sécurité</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Utilisez au minimum 8 caractères avec des lettres, des chiffres et des symboles pour garantir la sécurité de votre compte.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit button -->
            <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove>Modifier le mot de passe</span>
                    <span wire:loading.flex class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Modification en cours...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
