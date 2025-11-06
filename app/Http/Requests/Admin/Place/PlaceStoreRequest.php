<?php

namespace App\Http\Requests\Admin\Place;

use App\Http\Requests\Concerns\HasPhotoValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceStoreRequest extends FormRequest
{
    use HasPhotoValidationRules;

    /**
     * ID du lieu en cours d'édition (null en mode création)
     */
    protected ?int $placeId = null;

    /**
     * Définir l'ID du lieu pour la validation unique du slug en mode édition
     */
    public function setPlaceId(?int $placeId): self
    {
        $this->placeId = $placeId;

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
        $coordinateBounds = config('map.coordinates');

        $rules = [
            // Base place data
            'latitude' => [
                'required',
                'numeric',
                'min:'.$coordinateBounds['latitude']['min'],
                'max:'.$coordinateBounds['latitude']['max'],
            ],
            'longitude' => [
                'required',
                'numeric',
                'min:'.$coordinateBounds['longitude']['min'],
                'max:'.$coordinateBounds['longitude']['max'],
            ],
            'address' => 'required|string|max:255',
            'is_featured' => 'boolean',
            'placeRequestId' => 'nullable|integer|exists:place_requests,id',

            // Categories and Tags
            'categoryIds' => 'nullable|array',
            'categoryIds.*' => 'integer|exists:categories,id',
            'tagIds' => 'required|array|min:1',
            'tagIds.*' => 'integer|exists:tags,id',
        ];

        // Photos (règles centralisées via trait)
        $rules = array_merge($rules, $this->getPhotoValidationRules());

        // Dynamic translation rules for each supported locale
        foreach ($supportedLocales as $locale) {
            $rules["translations.{$locale}"] = 'required|array';
            $rules["translations.{$locale}.title"] = 'required|string|max:255';

            // Règle unique du slug avec exclusion du lieu actuel en mode édition
            $slugRule = Rule::unique('place_translations', 'slug')
                ->where('locale', $locale);

            if ($this->placeId !== null) {
                $slugRule->whereNot('place_id', $this->placeId);
            }

            $rules["translations.{$locale}.slug"] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $slugRule,
            ];

            $rules["translations.{$locale}.description"] = 'required|string';
            $rules["translations.{$locale}.practical_info"] = 'nullable|string';
            // Status désactivé : toutes les traductions sont automatiquement publiées
            // $rules["translations.{$locale}.status"] = ['nullable', Rule::in(['draft', 'published'])];
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
            // Base place data
            'latitude.required' => 'La latitude est obligatoire.',
            'latitude.numeric' => 'La latitude doit être un nombre.',
            'latitude.min' => 'La latitude doit être supérieure ou égale à :min.',
            'latitude.max' => 'La latitude doit être inférieure ou égale à :max.',

            'longitude.required' => 'La longitude est obligatoire.',
            'longitude.numeric' => 'La longitude doit être un nombre.',
            'longitude.min' => 'La longitude doit être supérieure ou égale à :min.',
            'longitude.max' => 'La longitude doit être inférieure ou égale à :max.',

            'address.required' => 'L\'adresse est obligatoire.',
            'address.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',

            'is_featured.boolean' => 'Le champ "À l\'affiche" doit être vrai ou faux.',

            'placeRequestId.exists' => 'La demande de lieu sélectionnée n\'existe pas.',

            // Categories and Tags
            'categoryIds.array' => 'Les catégories doivent être un tableau.',
            'categoryIds.*.exists' => 'Une ou plusieurs catégories sélectionnées n\'existent pas.',

            'tagIds.required' => 'Au moins un tag doit être sélectionné.',
            'tagIds.array' => 'Les tags doivent être un tableau.',
            'tagIds.min' => 'Au moins un tag doit être sélectionné.',
            'tagIds.*.exists' => 'Un ou plusieurs tags sélectionnés n\'existent pas.',
        ];

        // Photos (messages centralisés via trait)
        $messages = array_merge($messages, $this->getPhotoValidationMessages());

        // Dynamic translation error messages
        foreach ($supportedLocales as $locale) {
            $localeLabel = strtoupper($locale);

            $messages["translations.{$locale}.required"] = "Les traductions {$localeLabel} sont obligatoires.";

            $messages["translations.{$locale}.title.required"] = "Le titre {$localeLabel} est obligatoire.";
            $messages["translations.{$locale}.title.max"] = "Le titre {$localeLabel} ne peut pas dépasser 255 caractères.";

            $messages["translations.{$locale}.slug.required"] = "Le slug {$localeLabel} est obligatoire.";
            $messages["translations.{$locale}.slug.regex"] = "Le slug {$localeLabel} ne peut contenir que des lettres minuscules, chiffres et tirets.";
            $messages["translations.{$locale}.slug.unique"] = "Ce slug {$localeLabel} est déjà utilisé.";
            $messages["translations.{$locale}.slug.max"] = "Le slug {$localeLabel} ne peut pas dépasser 255 caractères.";

            $messages["translations.{$locale}.description.required"] = "La description {$localeLabel} est obligatoire.";

            // Status désactivé : toutes les traductions sont automatiquement publiées
            // $messages["translations.{$locale}.status.in"] = "Le statut {$localeLabel} doit être 'draft' ou 'published'.";
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
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'address' => 'adresse',
            'is_featured' => 'à l\'affiche',
            'placeRequestId' => 'demande de lieu',
            'categoryIds' => 'catégories',
            'tagIds' => 'tags',
            'photos' => 'photos',
        ];

        foreach ($supportedLocales as $locale) {
            $localeLabel = strtoupper($locale);
            $attributes["translations.{$locale}.title"] = "titre {$localeLabel}";
            $attributes["translations.{$locale}.slug"] = "slug {$localeLabel}";
            $attributes["translations.{$locale}.description"] = "description {$localeLabel}";
            $attributes["translations.{$locale}.practical_info"] = "informations pratiques {$localeLabel}";
            // Status désactivé : toutes les traductions sont automatiquement publiées
            // $attributes["translations.{$locale}.status"] = "statut {$localeLabel}";
        }

        return $attributes;
    }
}
