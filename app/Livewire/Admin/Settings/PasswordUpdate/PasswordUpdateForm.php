<?php

namespace App\Livewire\Admin\Settings\PasswordUpdate;

use App\Services\Admin\Settings\Password\PasswordUpdateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class PasswordUpdateForm extends Component
{
    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
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
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ];
    }

    /**
     * Update user password.
     */
    public function updatePassword(PasswordUpdateService $passwordUpdateService): void
    {
        $this->validate();

        // Vérifier l'autorisation via Policy
        $this->authorize('updateOwnPassword', [Auth::user(), Auth::user()]);

        try {
            $passwordUpdateService->updatePassword(
                Auth::user(),
                $this->password
            );

            // Reset form
            $this->reset(['password', 'password_confirmation']);

            // Message de succès (Livewire réactif, pas de redirection)
            $this->dispatch('flash-message', type: 'success', message: 'Votre mot de passe a été modifié avec succès.');

        } catch (\InvalidArgumentException $e) {
            $this->addError('password', $e->getMessage());
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.settings.password-update.password-update-form');
    }
}
