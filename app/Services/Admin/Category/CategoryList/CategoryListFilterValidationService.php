<?php

namespace App\Services\Admin\Category\CategoryList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryListFilterValidationService
{
    /**
     * Validate and clean filter inputs
     *
     * @param  array{search: string, activeFilter: string}  $filters
     * @return array{search: string, activeFilter: string}
     *
     * @throws ValidationException
     */
    public function validate(array $filters): array
    {
        $validator = Validator::make($filters, [
            'search' => 'nullable|string|max:255',
            'activeFilter' => 'required|string|in:all,active,inactive',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'search' => trim($validated['search'] ?? ''),
            'activeFilter' => $validated['activeFilter'],
        ];
    }
}
