<div x-data="{
        dropdown: {
            open: false,
            selectedAdminId: null,
            position: { top: 0, left: 0 }
        },
        openDropdown(adminId, button) {
            const rect = button.getBoundingClientRect();
            const newPosition = {
                top: rect.bottom + window.scrollY + 8,
                left: rect.right + window.scrollX - 224
            };

            // Si déjà ouvert, juste changer la position et l'ID (transition fluide)
            if (this.dropdown.open) {
                this.dropdown.selectedAdminId = adminId;
                this.dropdown.position = newPosition;
            } else {
                // Sinon, ouvrir normalement
                this.dropdown = {
                    open: true,
                    selectedAdminId: adminId,
                    position: newPosition
                };
            }
        },
        closeDropdown() {
            // Fermer le dropdown sans toucher à la position
            // La position sera écrasée lors de la prochaine ouverture
            this.dropdown.open = false;
            this.dropdown.selectedAdminId = null;
        }
     }"
     @update-finished.window="closeDropdown()">

    {{-- En-tête tableau: Statistiques et contrôles --}}
    <div class="mb-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900">{{ $admins->total() }}</span>
                <span class="text-gray-500">{{ $admins->total() > 1 ? 'administrateurs' : 'administrateur' }}</span>
            </div>

            @if($admins->total() > 0)
                <div class="hidden flex-row items-center gap-2 md:flex">
                    <div class="h-4 w-px bg-gray-200"></div>
                    <div class="text-xs text-gray-500">
                        {{ $admins->firstItem() }}-{{ $admins->lastItem() }} sur {{ $admins->total() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Sélecteur nombre par page --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Lignes par page</span>
            <select id="perPage"
                    wire:model.live="perPage"
                    wire:change="updatePerPage($event.target.value)"
                    class="rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-600">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- Table moderne --}}
    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-white">
        {{-- Loading overlay pour le tableau --}}
        <div wire:loading.delay.long>
            <div wire:loading.flex class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        {{-- Cercle extérieur animé --}}
                        <div class="h-12 w-12 rounded-full border-4 border-gray-200"></div>
                        <div class="absolute inset-0 h-12 w-12 animate-spin rounded-full border-4 border-transparent border-t-blue-600 border-r-blue-600"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-900">Chargement des administrateurs</p>
                        <p class="text-xs text-gray-500 mt-0.5">Veuillez patienter...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        {{-- Colonne Nom (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('name')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Nom</span>
                                @if($sortBy === 'name')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Email (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('email')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Email</span>
                                @if($sortBy === 'email')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Rôle (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('role')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Rôle</span>
                                @if($sortBy === 'role')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Date de création (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('created_at')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Créé le</span>
                                @if($sortBy === 'created_at')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Statut --}}
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-700">
                            Statut
                        </th>

                        {{-- Colonne Actions --}}
                        <th scope="col" class="relative px-6 py-3.5">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @error('load-data')
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-red-100 p-3 mb-4">
                                        <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-red-600" />
                                    </div>
                                    <h3 class="text-sm font-medium text-red-900 mb-1">Erreur de chargement</h3>
                                    <p class="text-xs text-red-600 max-w-sm">
                                        {{ $message }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @else
                        @forelse($admins as $admin)
                            <tr class="group relative hover:bg-gray-50/50 transition-colors"
                                wire:key="admin-row-{{ $admin->id }}">
                                {{-- Nom --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $admin->name }}
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        {{ $admin->email }}
                                    </div>
                                </td>

                                {{-- Rôle (badge) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($admin->role === 'super_admin')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-800 border border-purple-200">
                                            <x-heroicon-s-star class="h-3 w-3" />
                                            Super Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                            <x-heroicon-s-shield-check class="h-3 w-3" />
                                            Admin
                                        </span>
                                    @endif
                                </td>

                                {{-- Date de création --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                        <span class="text-xs text-gray-500">
                                            {{ $admin->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($admin->email_verified_at)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-800 border border-green-200">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-800 border border-yellow-200">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Invitation en attente
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions (bouton qui ouvre le dropdown unique) --}}
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <button @click.stop="openDropdown({{ $admin->id }}, $el)"
                                            type="button"
                                            class="flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
                                            :class="{ 'bg-gray-100 text-gray-600': dropdown.open && dropdown.selectedAdminId === {{ $admin->id }} }">
                                        <span class="sr-only">Ouvrir le menu</span>
                                        <x-heroicon-o-ellipsis-vertical class="h-5 w-5" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="rounded-full bg-gray-100 p-3 mb-4">
                                            <x-heroicon-o-users class="h-8 w-8 text-gray-400" />
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Aucun administrateur trouvé</h3>
                                        <p class="text-xs text-gray-500 max-w-sm">
                                            Aucun administrateur ne correspond à vos critères de recherche.
                                            Essayez de modifier vos filtres.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @enderror
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($admins->hasPages())
            <div class="border-t border-gray-200 bg-white px-4 py-3">
                {{ $admins->links('vendor.livewire.tailwind-modern') }}
            </div>
        @endif
    </div>

    {{-- Dropdown unique (positionné dynamiquement) --}}
    <div x-show="dropdown.open"
         @click.away="closeDropdown()"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         :style="`position: fixed; top: ${dropdown.position.top}px; left: ${dropdown.position.left}px; z-index: 9999;`"
         class="w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none;">
        <div class="py-1">
            {{-- Changer le rôle --}}
            <button type="button"
                    @click="$wire.openRoleModal(dropdown.selectedAdminId); closeDropdown();"
                    class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <x-heroicon-o-arrow-path-rounded-square class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-600" />
                Changer le rôle
            </button>

            {{-- Séparateur --}}
            <div class="border-t border-gray-100"></div>

            {{-- Supprimer --}}
            <button type="button"
                    @click="$wire.openDeleteModal(dropdown.selectedAdminId); closeDropdown();"
                    class="group flex w-full items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                <x-heroicon-o-trash class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-600" />
                Supprimer
            </button>
        </div>
    </div>

    {{-- Modale de suppression --}}
    <div x-show="$wire.deleteModalOpen"
         x-transition:enter="transition ease-out duration-50"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        {{-- Modale centrée --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="$wire.deleteModalOpen"
                 @click.away="$wire.closeDeleteModal()"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md rounded-xl bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between border-b border-gray-100 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Confirmer la suppression</h3>
                            <p class="mt-0.5 text-sm text-gray-500">Cette action est irréversible</p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    <p class="text-sm text-gray-600">
                        Êtes-vous sûr de vouloir supprimer cet administrateur ? Toutes ses données seront définitivement supprimées de la plateforme.
                    </p>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <button type="button"
                            wire:click="closeDeleteModal"
                            class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="confirmDelete"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modale de changement de rôle --}}
    <div x-show="$wire.roleModalOpen"
         x-transition:enter="transition ease-out duration-50"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        {{-- Modale centrée --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="$wire.roleModalOpen"
                 @click.away="$wire.closeRoleModal()"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md rounded-xl bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between border-b border-gray-100 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                            <x-heroicon-o-arrow-path-rounded-square class="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Modifier le rôle</h3>
                            <p class="mt-0.5 text-sm text-gray-500">Changez les permissions de l'administrateur</p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    <label for="newRole" class="block text-sm font-medium text-gray-700 mb-2">
                        Nouveau rôle
                    </label>
                    <div class="space-y-2">
                        <label class="relative flex items-center rounded-lg border border-gray-200 p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                               :class="{ 'border-blue-500 bg-blue-50': $wire.newRole === 'admin' }">
                            <input type="radio"
                                   wire:model="newRole"
                                   value="admin"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-s-shield-check class="h-5 w-5 text-blue-600" />
                                    <span class="text-sm font-medium text-gray-900">Administrateur</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Peut gérer les lieux, tags et catégories</p>
                            </div>
                        </label>

                        <label class="relative flex items-center rounded-lg border border-gray-200 p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                               :class="{ 'border-purple-500 bg-purple-50': $wire.newRole === 'super_admin' }">
                            <input type="radio"
                                   wire:model="newRole"
                                   value="super_admin"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500">
                            <div class="ml-3">
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-s-star class="h-5 w-5 text-purple-600" />
                                    <span class="text-sm font-medium text-gray-900">Super Administrateur</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Accès complet + gestion des administrateurs</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <button type="button"
                            wire:click="closeRoleModal"
                            class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2">
                        Annuler
                    </button>
                    <button type="button"
                            wire:click="confirmRoleChange"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Modifier le rôle
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
