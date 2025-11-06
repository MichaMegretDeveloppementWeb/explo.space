<?php

namespace App\Http\Requests\Web\Place;

use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'type' => ['required', Rule::in(['signalement', 'modification'])],
            'recaptcha_token' => ['required', new RecaptchaRule],
        ];

        $rules['description'] = $this->input('type') === 'modification'
            ? ['nullable', 'string', 'min:10', 'max:2000']
            : ['required', 'string', 'min:10', 'max:2000'];

        // Validation conditionnelle selon le type
        if ($this->input('type') === 'modification') {
            $rules['selected_fields'] = ['required', 'array', 'min:1'];
            $rules['selected_fields.*'] = [Rule::in(['title', 'description', 'coordinates', 'address', 'practical_info'])];

            // Récupérer les champs sélectionnés
            $selectedFields = $this->input('selected_fields', []);

            // Validation des nouvelles valeurs
            $rules['new_values'] = ['required', 'array'];

            // Titre : required si sélectionné, sinon nullable
            $rules['new_values.title'] = in_array('title', $selectedFields)
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'];

            // Description : required si sélectionnée, sinon nullable
            $rules['new_values.description'] = in_array('description', $selectedFields)
                ? ['required', 'string', 'max:5000']
                : ['nullable', 'string', 'max:5000'];

            // Coordonnées : required si sélectionnées, sinon nullable
            if (in_array('coordinates', $selectedFields)) {
                $rules['new_values.coordinates'] = ['required', 'array'];

                // Utiliser les limites de config/map.php pour les coordonnées
                $latMin = config('map.coordinates.latitude.min');
                $latMax = config('map.coordinates.latitude.max');
                $lngMin = config('map.coordinates.longitude.min');
                $lngMax = config('map.coordinates.longitude.max');

                $rules['new_values.coordinates.lat'] = ['required', 'numeric', "between:$latMin,$latMax"];
                $rules['new_values.coordinates.lng'] = ['required', 'numeric', "between:$lngMin,$lngMax"];
            } else {
                $rules['new_values.coordinates'] = ['nullable', 'array'];
                $rules['new_values.coordinates.lat'] = ['nullable', 'numeric'];
                $rules['new_values.coordinates.lng'] = ['nullable', 'numeric'];
            }

            // Adresse : required si sélectionnée, sinon nullable
            $rules['new_values.address'] = in_array('address', $selectedFields)
                ? ['required', 'string', 'max:500']
                : ['nullable', 'string', 'max:500'];

            // Informations pratiques : required si sélectionnées, sinon nullable
            $rules['new_values.practical_info'] = in_array('practical_info', $selectedFields)
                ? ['required', 'string', 'max:2000']
                : ['nullable', 'string', 'max:2000'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Récupérer les limites depuis config pour les messages dynamiques
        $latMin = config('map.coordinates.latitude.min');
        $latMax = config('map.coordinates.latitude.max');
        $lngMin = config('map.coordinates.longitude.min');
        $lngMax = config('map.coordinates.longitude.max');

        return [
            // Champs généraux
            'type.required' => __('web/validation/edit-request.type.required'),
            'type.in' => __('web/validation/edit-request.type.in'),
            'description.required' => __('web/validation/edit-request.description.required'),
            'description.min' => __('web/validation/edit-request.description.min', ['min' => 10]),
            'description.max' => __('web/validation/edit-request.description.max', ['max' => 2000]),
            'contact_email.required' => __('web/validation/edit-request.contact_email.required'),
            'contact_email.email' => __('web/validation/edit-request.contact_email.email'),
            'contact_email.max' => __('web/validation/edit-request.contact_email.max', ['max' => 255]),

            // Champs sélectionnés
            'selected_fields.required' => __('web/validation/edit-request.selected_fields.required'),
            'selected_fields.min' => __('web/validation/edit-request.selected_fields.min'),
            'selected_fields.array' => __('web/validation/edit-request.selected_fields.array'),
            'selected_fields.*.in' => __('web/validation/edit-request.selected_fields.in'),

            // Nouvelles valeurs - titre
            'new_values.title.required' => __('web/validation/edit-request.new_values.title.required'),
            'new_values.title.max' => __('web/validation/edit-request.new_values.title.max', ['max' => 255]),

            // Nouvelles valeurs - description
            'new_values.description.required' => __('web/validation/edit-request.new_values.description.required'),
            'new_values.description.max' => __('web/validation/edit-request.new_values.description.max', ['max' => 5000]),

            // Nouvelles valeurs - adresse
            'new_values.address.required' => __('web/validation/edit-request.new_values.address.required'),
            'new_values.address.max' => __('web/validation/edit-request.new_values.address.max', ['max' => 500]),

            // Nouvelles valeurs - informations pratiques
            'new_values.practical_info.required' => __('web/validation/edit-request.new_values.practical_info.required'),
            'new_values.practical_info.max' => __('web/validation/edit-request.new_values.practical_info.max', ['max' => 2000]),

            // Nouvelles valeurs - coordonnées (utilise les limites de config)
            'new_values.coordinates.lat.required' => __('web/validation/edit-request.new_values.coordinates.lat.required'),
            'new_values.coordinates.lat.numeric' => __('web/validation/edit-request.new_values.coordinates.lat.numeric'),
            'new_values.coordinates.lat.between' => __('web/validation/edit-request.new_values.coordinates.lat.between', ['min' => $latMin, 'max' => $latMax]),

            'new_values.coordinates.lng.required' => __('web/validation/edit-request.new_values.coordinates.lng.required'),
            'new_values.coordinates.lng.numeric' => __('web/validation/edit-request.new_values.coordinates.lng.numeric'),
            'new_values.coordinates.lng.between' => __('web/validation/edit-request.new_values.coordinates.lng.between', ['min' => $lngMin, 'max' => $lngMax]),

            // reCAPTCHA
            'recaptcha_token.required' => __('web/validation/edit-request.recaptcha.required'),
        ];
    }
}
