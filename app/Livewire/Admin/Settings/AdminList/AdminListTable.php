<?php

namespace App\Livewire\Admin\Settings\AdminList;

use App\Livewire\Admin\Settings\AdminList\Concerns\ManagesLoadData;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AdminListTable extends Component
{
    use ManagesLoadData;
    use WithPagination;

    /**
     * Vue de pagination personnalisée
     */
    protected string $paginationTheme = 'tailwind-modern';

    /**
     * Filtre appliqué
     */
    public string $search = '';

    /**
     * Tri appliqué
     */
    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    /**
     * Pagination
     */
    public int $perPage = 20;

    /**
     * Modales et actions
     */
    public bool $deleteModalOpen = false;

    public ?int $adminToDelete = null;

    public bool $roleModalOpen = false;

    public ?int $adminToEdit = null;

    public ?string $newRole = null;

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.settings.admin-list.admin-list-table', [
            'admins' => $this->loadAdmins(),
        ]);
    }

    /**
     * Ouvrir la modale de suppression
     */
    public function openDeleteModal(int $adminId): void
    {
        $this->adminToDelete = $adminId;
        $this->deleteModalOpen = true;
    }

    /**
     * Fermer la modale de suppression
     */
    public function closeDeleteModal(): void
    {
        $this->deleteModalOpen = false;
        $this->adminToDelete = null;
    }

    /**
     * Confirmer la suppression
     */
    public function confirmDelete(): void
    {
        if (! $this->adminToDelete) {
            $this->dispatch('flash-message', type: 'error', message: 'Aucun administrateur sélectionné.');
            $this->closeDeleteModal();

            return;
        }

        $targetAdmin = User::find($this->adminToDelete);

        if (! $targetAdmin) {
            $this->dispatch('flash-message', type: 'error', message: 'Administrateur introuvable.');
            $this->closeDeleteModal();

            return;
        }

        // Vérifier les permissions via la Policy
        if (! auth()->user()->can('deleteAdmin', $targetAdmin)) {
            $this->dispatch('flash-message', type: 'error', message: 'Vous n\'avez pas la permission de supprimer cet administrateur.');
            $this->closeDeleteModal();

            return;
        }

        try {
            $adminName = $targetAdmin->name;

            // Supprimer l'administrateur
            $targetAdmin->delete();

            $this->dispatch('flash-message', type: 'success', message: "L'administrateur {$adminName} a été supprimé avec succès.");
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression d\'un administrateur', [
                'admin_id' => $this->adminToDelete,
                'user_id' => auth()->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Une erreur est survenue lors de la suppression. Veuillez réessayer.';
            if (config('app.debug')) {
                $errorMessage .= ' : '.$e->getMessage();
            }
            $this->dispatch('flash-message', type: 'error', message: $errorMessage);
        }

        $this->closeDeleteModal();
        $this->resetPage();
    }

    /**
     * Ouvrir la modale de changement de rôle
     */
    public function openRoleModal(int $adminId): void
    {
        $admin = User::find($adminId);

        if (! $admin) {
            $this->dispatch('flash-message', type: 'error', message: 'Administrateur introuvable.');

            return;
        }

        $this->adminToEdit = $adminId;
        $this->newRole = $admin->role;
        $this->roleModalOpen = true;
    }

    /**
     * Fermer la modale de changement de rôle
     */
    public function closeRoleModal(): void
    {
        $this->roleModalOpen = false;
        $this->adminToEdit = null;
        $this->newRole = null;
    }

    /**
     * Confirmer le changement de rôle
     */
    public function confirmRoleChange(): void
    {
        if (! $this->adminToEdit || ! $this->newRole) {
            $this->dispatch('flash-message', type: 'error', message: 'Informations manquantes pour le changement de rôle.');
            $this->closeRoleModal();

            return;
        }

        // Valider le rôle
        if (! in_array($this->newRole, ['admin', 'super_admin'])) {
            $this->dispatch('flash-message', type: 'error', message: 'Rôle invalide.');
            $this->closeRoleModal();

            return;
        }

        $targetAdmin = User::find($this->adminToEdit);

        if (! $targetAdmin) {
            $this->dispatch('flash-message', type: 'error', message: 'Administrateur introuvable.');
            $this->closeRoleModal();

            return;
        }

        // Vérifier les permissions via la Policy
        if (! auth()->user()->can('changeRole', $targetAdmin)) {
            $this->dispatch('flash-message', type: 'error', message: 'Vous n\'avez pas la permission de modifier le rôle de cet administrateur.');
            $this->closeRoleModal();

            return;
        }

        // Vérifier si le rôle a changé
        if ($targetAdmin->role === $this->newRole) {
            $this->dispatch('flash-message', type: 'info', message: 'Aucun changement détecté : le rôle est déjà identique.');
            $this->closeRoleModal();

            return;
        }

        try {
            $oldRole = $targetAdmin->role === 'super_admin' ? 'Super Administrateur' : 'Administrateur';
            $newRole = $this->newRole === 'super_admin' ? 'Super Administrateur' : 'Administrateur';

            // Modifier le rôle
            $targetAdmin->role = $this->newRole;
            $targetAdmin->save();

            $this->dispatch('flash-message', type: 'success', message: "Le rôle de {$targetAdmin->name} a été modifié de {$oldRole} à {$newRole}.");
        } catch (\Exception $e) {
            \Log::error('Erreur lors du changement de rôle d\'un administrateur', [
                'admin_id' => $this->adminToEdit,
                'new_role' => $this->newRole,
                'user_id' => auth()->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Une erreur est survenue lors de la modification. Veuillez réessayer.';
            if (config('app.debug')) {
                $errorMessage .= ' : '.$e->getMessage();
            }
            $this->dispatch('flash-message', type: 'error', message: $errorMessage);
        }

        $this->closeRoleModal();
        $this->resetPage();
    }

    /**
     * Écouter les changements de filtres
     */
    #[On('filters:updated')]
    public function updateFiltersFromEvent(string $search): void
    {
        $this->search = $search;

        // Réinitialiser la pagination lors d'un changement de filtre
        $this->resetPage();
    }

    /**
     * Trier par colonne (toggle ASC/DESC)
     */
    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            // Toggle direction
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Nouvelle colonne, direction par défaut DESC
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }

        $this->resetPage();
    }

    /**
     * Changer le nombre d'éléments par page
     */
    public function updatePerPage(int $value): void
    {
        $this->perPage = $value;
        $this->resetPage();
    }
}
