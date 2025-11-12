<?php

namespace App\Livewire\Admin\Settings\UserCreate;

use App\Services\Admin\Settings\User\UserCreateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserCreateForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $role = 'admin';

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,super_admin'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être Admin ou Super Admin.',
        ];
    }

    /**
     * Create a new admin user.
     */
    public function createUser(UserCreateService $userCreateService): void
    {
        $this->validate();

        // Vérifier l'autorisation via Policy
        $this->authorize('createUser', Auth::user());

        try {
            $userCreateService->createAdmin([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ]);

            // Reset form
            $this->reset(['name', 'email', 'role']);
            $this->role = 'admin'; // Reset to default

            // Message de succès (Livewire réactif, pas de redirection)
            $this->dispatch('flash-message', type: 'success', message: 'L\'administrateur a été créé avec succès. Un email d\'invitation a été envoyé.');

        } catch (\InvalidArgumentException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.settings.user-create.user-create-form');
    }
}
