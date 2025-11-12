<?php

namespace App\Livewire\Admin\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPasswordForm extends Component
{
    public string $email = '';

    /**
     * Validation rules.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
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
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
        ];
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->reset('email');
            $this->dispatch('flash-message', type: 'success', message: 'Un lien de réinitialisation a été envoyé à votre adresse email.');
        } else {
            $this->addError('email', 'Impossible de trouver un utilisateur avec cette adresse email.');
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.auth.forgot-password-form');
    }
}
