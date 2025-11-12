<?php

namespace App\Livewire\Web\Contact;

use App\Mail\ContactMessageMail;
use App\Rules\RecaptchaRule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public string $recaptcha_token = '';

    public bool $success = false;

    public string $errorMessage = '';

    /**
     * Render du composant
     */
    public function render(): View
    {
        return view('livewire.web.contact.contact-form');
    }

    /**
     * Règles de validation
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'recaptcha_token' => ['required', new RecaptchaRule],
        ];
    }

    /**
     * Attributs de validation personnalisés
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => strtolower(__('web/pages/contact.form.fields.name.label')),
            'email' => strtolower(__('web/pages/contact.form.fields.email.label')),
            'subject' => strtolower(__('web/pages/contact.form.fields.subject.label')),
            'message' => strtolower(__('web/pages/contact.form.fields.message.label')),
            'recaptcha_token' => 'reCAPTCHA',
        ];
    }

    /**
     * Messages de validation personnalisés
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('web/validation/contact.name.required'),
            'name.min' => __('web/validation/contact.name.min'),
            'name.max' => __('web/validation/contact.name.max'),
            'email.required' => __('web/validation/contact.email.required'),
            'email.email' => __('web/validation/contact.email.email'),
            'email.max' => __('web/validation/contact.email.max'),
            'subject.max' => __('web/validation/contact.subject.max'),
            'message.required' => __('web/validation/contact.message.required'),
            'message.min' => __('web/validation/contact.message.min'),
            'message.max' => __('web/validation/contact.message.max'),
            'recaptcha_token.required' => __('web/validation/contact.recaptcha_token.required'),
        ];
    }

    /**
     * Validation temps réel du nom
     */
    /*public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }*/

    /**
     * Soumettre le formulaire de contact
     *
     * @param  string  $recaptchaToken  Token reCAPTCHA v3 obtenu par Alpine.js/JavaScript
     */
    public function submit(string $recaptchaToken = ''): void
    {
        // Reset messages
        $this->success = false;
        $this->errorMessage = '';

        // Si un token est passé en paramètre, l'utiliser
        if (! empty($recaptchaToken)) {
            $this->recaptcha_token = $recaptchaToken;
        }

        // Rate limiting par IP (3 messages par heure)
        $key = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->errorMessage = __('web/pages/contact.form.errors.rate_limit');

            return;
        }

        // Validation
        $validated = $this->validate();

        try {
            // Envoyer l'email
            Mail::to(config('mail.destination_mail_contact'))
                ->send(new ContactMessageMail(
                    name: $validated['name'],
                    email: $validated['email'],
                    messageSubject: $validated['subject'] ?? null,
                    messageContent: $validated['message']
                ));

            // Incrémenter le rate limiter
            RateLimiter::hit($key, 600); // 10 minutes

            // Succès
            $this->success = true;

            // Reset form
            $this->reset(['name', 'email', 'subject', 'message', 'recaptcha_token']);

            // Dispatch événement pour recharger reCAPTCHA
            $this->dispatch('contact-form-submitted');
        } catch (\Exception $e) {
            // Log l'erreur
            logger()->error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);

            $this->errorMessage = __('web/pages/contact.form.errors.message');
        }
    }

    /**
     * Gérer les erreurs reCAPTCHA
     */
    public function handleRecaptchaError(string $errorMessage): void
    {
        logger()->error('reCAPTCHA error on contact form', [
            'error' => $errorMessage,
            'ip' => request()->ip(),
        ]);

        $this->errorMessage = __('web/pages/contact.form.errors.recaptcha');
    }
}
