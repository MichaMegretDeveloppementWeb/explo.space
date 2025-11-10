<?php

namespace App\Services\Admin\Tag\TagList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TagListFilterValidationService
{
    /**
     * Validate and clean filter inputs
     *
     * @param  array{search: string, activeFilter: string, locale: string}  $filters
     * @return array{search: string, activeFilter: string, locale: string}
     *
     * @throws ValidationException
     */
    public function validate(array $filters): array
    {
        $validator = Validator::make($filters, [
            'search' => 'nullable|string|max:255',
            'activeFilter' => 'required|string|in:all,active,inactive',
            'locale' => 'required|string|in:'.implode(',', config('locales.supported', ['fr', 'en'])),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'search' => trim($validated['search'] ?? ''),
            'activeFilter' => $validated['activeFilter'],
            'locale' => $validated['locale'],
        ];
    }
}
