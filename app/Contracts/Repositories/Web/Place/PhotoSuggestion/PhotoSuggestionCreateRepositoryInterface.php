<?php

namespace App\Contracts\Repositories\Web\Place\PhotoSuggestion;

use App\Models\EditRequest;

interface PhotoSuggestionCreateRepositoryInterface
{
    /**
     * Create a new photo suggestion edit request
     *
     * @param  array{place_id: int, type: string, contact_email: string, suggested_changes: array{photos: array<int, string>}, detected_language: string, status: string}  $data
     */
    public function create(array $data): EditRequest;
}
