<?php

namespace App\Services\Admin\Category\CategoryList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryListSortingValidationService
{
    /**
     * Allowed sortable columns
     */
    private const ALLOWED_SORT_COLUMNS = [
        'name',
        'created_at',
        'updated_at',
        'is_active',
        'places_count',
    ];

    /**
     * Validate and clean sorting inputs
     *
     * @param  array{sortBy: string, sortDirection: string}  $sorting
     * @return array{column: string, direction: string}
     *
     * @throws ValidationException
     */
    public function validate(array $sorting): array
    {
        $validator = Validator::make($sorting, [
            'sortBy' => 'required|string|in:'.implode(',', self::ALLOWED_SORT_COLUMNS),
            'sortDirection' => 'required|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return [
            'column' => $validated['sortBy'],
            'direction' => $validated['sortDirection'],
        ];
    }
}
