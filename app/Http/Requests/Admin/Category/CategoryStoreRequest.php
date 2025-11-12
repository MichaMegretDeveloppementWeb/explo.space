<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryStoreRequest extends FormRequest
{
    /**
     * ID de la catégorie en cours d'édition (null en mode création)
     */
    protected ?int $categoryId = null;

    /**
     * Définir l'ID de la catégorie pour la validation unique du slug en mode édition
     */
    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAdminRights();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Règle unique du slug avec exclusion de la catégorie actuelle en mode édition
        $slugRule = Rule::unique('categories', 'slug');

        if ($this->categoryId !== null) {
            $slugRule->ignore($this->categoryId);
        }

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $slugRule,
            ],
            'description' => 'nullable|string|max:2000',
            'color' => [
                'required',
                'string',
                'max:50',
                // Accept various color formats: hex (#FFF, #FFFFFF), rgb, rgba, hsl, color names
                'regex:/^(#([0-9A-Fa-f]{3}){1,2}|rgb\(.*\)|rgba\(.*\)|hsl\(.*\)|hsla\(.*\)|[a-z]+)$/',
            ],
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'slug.required' => 'Le slug est obligatoire.',
            'slug.unique' => 'Ce slug est déjà utilisé par une autre catégorie.',
            'slug.regex' => 'Le slug doit contenir uniquement des lettres minuscules, chiffres et tirets.',
            'description.max' => 'La description ne doit pas dépasser 2000 caractères.',
            'color.required' => 'La couleur est obligatoire.',
            'color.regex' => 'Le format de couleur est invalide.',
        ];
    }
}
