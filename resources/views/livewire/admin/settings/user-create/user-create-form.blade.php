<div>
    <form wire:submit="createUser">
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <h3 class="text-lg font-medium text-gray-900">Créer un administrateur</h3>
                <p class="mt-1 text-sm text-gray-600">Le nouvel administrateur recevra un email d'invitation pour définir son mot de passe</p>
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
                    placeholder="Nom complet de l'administrateur"
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
                    placeholder="email@example.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Rôle
                </label>
                <select
                    wire:model="role"
                    id="role"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors @error('role') border-red-500 @enderror"
                >
                    <option value="admin">Administrateur</option>
                    <option value="super_admin">Super Administrateur</option>
                </select>
                @error('role')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-envelope class="h-5 w-5 text-blue-500" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Email d'invitation</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Un email sera automatiquement envoyé à l'adresse indiquée avec un lien valide 7 jours pour définir le mot de passe.
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
                    <span wire:loading.remove>Créer l'administrateur</span>
                    <span wire:loading.flex class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Création en cours...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
