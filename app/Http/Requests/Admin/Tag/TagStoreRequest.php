<?php

namespace App\Http\Requests\Admin\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagStoreRequest extends FormRequest
{
    /**
     * ID du tag en cours d'édition (null en mode création)
     */
    protected ?int $tagId = null;

    /**
     * Définir l'ID du tag pour la validation unique du slug en mode édition
     */
    public function setTagId(?int $tagId): self
    {
        $this->tagId = $tagId;

        return $this;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $supportedLocales = config('locales.supported', ['fr', 'en']);

        $rules = [
            // Base tag data
            'color' => [
                'required',
                'string',
                'max:50',
                // Accept various color formats: hex (#FFF, #FFFFFF), rgb, rgba, hsl, color names
                'regex:/^(#([0-9A-Fa-f]{3}){1,2}|rgb\(.*\)|rgba\(.*\)|hsl\(.*\)|hsla\(.*\)|[a-z]+)$/',
            ],
            'is_active' => 'boolean',
        ];

        // Dynamic translation rules for each supported locale
        foreach ($supportedLocales as $locale) {
            $rules["translations.{$locale}"] = 'required|array';
            $rules["translations.{$locale}.name"] = 'required|string|max:255';

            // Règle unique du slug avec exclusion du tag actuel en mode édition
            $slugRule = Rule::unique('tag_translations', 'slug')
                ->where('locale', $locale);

            if ($this->tagId !== null) {
                $slugRule->whereNot('tag_id', $this->tagId);
            }

            $rules["translations.{$locale}.slug"] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $slugRule,
            ];

            $rules["translations.{$locale}.description"] = 'nullable|string|max:2000';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $supportedLocales = config('locales.supported', ['fr', 'en']);
        $messages = [
            // Base tag data
            'color.required' => 'La couleur est obligatoire.',
            'color.string' => 'La couleur doit être une chaîne de caractères.',
            'color.max' => 'La couleur ne peut pas dépasser 50 caractères.',
            'color.regex' => 'La couleur doit être au format valide (hex, rgb, rgba, hsl, hsla ou nom de couleur).',

            'is_active.boolean' => 'Le champ "Actif" doit être vrai ou faux.',
        ];

        // Dynamic translation error messages
        foreach ($supportedLocales as $locale) {
            $localeLabel = strtoupper($locale);

            $messages["translations.{$locale}.required"] = "Les traductions {$localeLabel} sont obligatoires.";

            $messages["translations.{$locale}.name.required"] = "Le nom {$localeLabel} est obligatoire.";
            $messages["translations.{$locale}.name.max"] = "Le nom {$localeLabel} ne peut pas dépasser 255 caractères.";

            $messages["translations.{$locale}.slug.required"] = "Le slug {$localeLabel} est obligatoire.";
            $messages["translations.{$locale}.slug.regex"] = "Le slug {$localeLabel} ne peut contenir que des lettres minuscules, chiffres et tirets.";
            $messages["translations.{$locale}.slug.unique"] = "Ce slug {$localeLabel} est déjà utilisé.";
            $messages["translations.{$locale}.slug.max"] = "Le slug {$localeLabel} ne peut pas dépasser 255 caractères.";

            $messages["translations.{$locale}.description.max"] = "La description {$localeLabel} ne peut pas dépasser 2000 caractères.";
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $supportedLocales = config('locales.supported', ['fr', 'en']);
        $attributes = [
            'color' => 'couleur',
            'is_active' => 'actif',
        ];

        foreach ($supportedLocales as $locale) {
            $localeLabel = strtoupper($locale);
            $attributes["translations.{$locale}.name"] = "nom {$localeLabel}";
            $attributes["translations.{$locale}.slug"] = "slug {$localeLabel}";
            $attributes["translations.{$locale}.description"] = "description {$localeLabel}";
        }

        return $attributes;
    }
}
