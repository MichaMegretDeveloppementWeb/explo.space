<div>
    <form wire:submit="authenticate" class="space-y-5">

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Adresse email
            </label>
            <input
                wire:model="email"
                type="email"
                id="email"
                name="email"
                autocomplete="email"
                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('email') border-red-500 ring-2 ring-red-100 @enderror"
                placeholder="admin@explo.space"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-600 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Mot de passe
            </label>
            <input
                wire:model="password"
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-500 ring-2 ring-red-100 @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-1.5 text-xs text-red-600 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Remember me + Forgot password -->
        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center">
                <input
                    wire:model="remember"
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-600">
                    Se souvenir de moi
                </label>
            </div>

            <div>
                <a href="{{ route('admin.password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700 transition-colors font-medium">
                    Mot de passe oublié ?
                </a>
            </div>
        </div>

        <!-- Submit button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm"
        >
            <span wire:loading.remove>
                Se connecter
            </span>
            <span wire:loading.flex class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2.5 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Connexion en cours...
            </span>
        </button>
    </form>
</div>
