<?php

namespace App\Livewire\Admin\Settings\ProfileUpdate;

use App\Services\Admin\Settings\Profile\ProfileUpdateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileUpdateForm extends Component
{
    public string $name = '';

    public string $email = '';

    /**
     * Mount component with current user data.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.Auth::id()],
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
        ];
    }

    /**
     * Update user profile.
     */
    public function updateProfile(ProfileUpdateService $profileUpdateService): void
    {
        $this->validate();

        // Vérifier l'autorisation via Policy
        $this->authorize('updateOwnProfile', [Auth::user(), Auth::user()]);

        try {
            $profileUpdateService->updateProfile(Auth::user(), [
                'name' => $this->name,
                'email' => $this->email,
            ]);

            // Message de succès (Livewire réactif, pas de redirection)
            if ($this->email !== Auth::user()->email) {
                $this->dispatch('flash-message', type: 'info', message: 'Votre profil a été mis à jour. Veuillez vérifier votre nouvelle adresse email.');
            } else {
                $this->dispatch('flash-message', type: 'success', message: 'Votre profil a été mis à jour avec succès.');
            }

            // Dispatch event to refresh navbar
            $this->dispatch('profile:updated');

        } catch (\InvalidArgumentException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.settings.profile-update.profile-update-form');
    }
}
