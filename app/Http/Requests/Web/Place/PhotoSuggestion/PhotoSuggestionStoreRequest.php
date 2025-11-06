<?php

namespace App\Http\Requests\Web\Place\PhotoSuggestion;

use App\Http\Requests\Concerns\HasPhotoValidationRules;
use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

class PhotoSuggestionStoreRequest extends FormRequest
{
    use HasPhotoValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public access
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'contact_email' => ['required', 'email', 'max:255'],
            'recaptcha_token' => ['required', new RecaptchaRule],
        ];

        // Photos validation (required with min 1)
        $photoRules = $this->getPhotoValidationRules('photos');
        $photoRules['photos'] = ['required', 'array', 'min:1', 'max:'.config('upload.images.max_files')];

        return array_merge($rules, $photoRules);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxFiles = config('upload.images.max_files');

        $messages = [
            'contact_email.required' => 'Votre email de contact est obligatoire.',
            'contact_email.email' => 'L\'adresse email doit être valide.',
            'contact_email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',

            'photos.required' => 'Vous devez sélectionner au moins une photo.',
            'photos.min' => 'Vous devez sélectionner au moins une photo.',
            'photos.max' => "Vous ne pouvez pas télécharger plus de {$maxFiles} photos.",

            'recaptcha_token.required' => 'La validation reCAPTCHA a échoué. Veuillez réessayer.',
        ];

        // Merge photo validation messages
        return array_merge($messages, $this->getPhotoValidationMessages('photos'));
    }
}
