<?php

namespace App\Services\Admin\Category\CategoryList;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryListPaginationValidationService
{
    /**
     * Allowed per-page values
     */
    private const ALLOWED_PER_PAGE = [10, 20, 50, 100];

    /**
     * Default per-page value
     */
    private const DEFAULT_PER_PAGE = 20;

    /**
     * Validate and clean pagination input
     *
     * @param  array{perPage: int}  $pagination
     * @return int Clean perPage value
     *
     * @throws ValidationException
     */
    public function validate(array $pagination): int
    {
        $validator = Validator::make($pagination, [
            'perPage' => 'required|integer|in:'.implode(',', self::ALLOWED_PER_PAGE),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        return $validated['perPage'] ?? self::DEFAULT_PER_PAGE;
    }
}
