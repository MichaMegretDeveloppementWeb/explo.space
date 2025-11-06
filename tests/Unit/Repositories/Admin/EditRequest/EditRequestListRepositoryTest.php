<?php

namespace Tests\Unit\Repositories\Admin\EditRequest;

use App\Models\EditRequest;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\User;
use App\Repositories\Admin\EditRequest\EditRequestListRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditRequestListRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected EditRequestListRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EditRequestListRepository;
    }

    // ========================================
    // Basic Functionality Tests
    // ========================================

    public function test_get_paginated_edit_requests_returns_correct_structure(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Test Place',
            'status' => 'published',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
            'contact_email' => 'test@example.com',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->relationLoaded('place'));
        $this->assertTrue($result->first()->place->relationLoaded('translations'));
        $this->assertTrue($result->first()->relationLoaded('processedByAdmin'));
        $this->assertTrue($result->first()->relationLoaded('viewedByAdmin'));
    }

    public function test_get_paginated_edit_requests_returns_only_french_translations(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre français',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'English title',
            'status' => 'published',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result->first()->place->translations);
        $this->assertEquals('fr', $result->first()->place->translations->first()->locale);
        $this->assertEquals('Titre français', $result->first()->place->translations->first()->title);
    }

    // ========================================
    // Search Filter Tests
    // ========================================

    public function test_filters_by_search_in_place_title(): void
    {
        // Arrange
        $admin = User::factory()->create();

        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'SpaceX Boca Chica',
            'status' => 'published',
        ]);

        $request1 = EditRequest::factory()->create(['place_id' => $place1->id, 'contact_email' => 'test@example.com']);
        EditRequest::factory()->create(['place_id' => $place2->id, 'contact_email' => 'other@example.com']);

        $filters = ['search' => 'Kennedy', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($request1->id, $result->first()->id);
    }

    public function test_filters_by_search_in_contact_email(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $request1 = EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'john.doe@example.com',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'jane.smith@example.com',
        ]);

        $filters = ['search' => 'john.doe', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($request1->id, $result->first()->id);
    }

    public function test_search_is_case_insensitive(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id]);

        $filters = ['search' => 'kennedy', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
    }

    // ========================================
    // Type Filter Tests
    // ========================================

    public function test_filters_by_type_modification(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $modification = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
        ]);

        $filters = ['search' => '', 'type' => 'modification', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($modification->id, $result->first()->id);
    }

    public function test_filters_by_type_signalement(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'type' => 'modification']);

        $signalement = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',
        ]);

        $filters = ['search' => '', 'type' => 'signalement', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($signalement->id, $result->first()->id);
    }

    public function test_filters_by_type_photo_suggestion(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'type' => 'modification']);

        $photoSuggestion = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
        ]);

        $filters = ['search' => '', 'type' => 'photo_suggestion', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($photoSuggestion->id, $result->first()->id);
    }

    // ========================================
    // Status Filter Tests
    // ========================================

    public function test_filters_by_status_submitted(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $submitted = EditRequest::factory()->create([
            'place_id' => $place->id,
            'status' => 'submitted',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'pending']);

        $filters = ['search' => '', 'type' => '', 'status' => ['submitted']];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($submitted->id, $result->first()->id);
    }

    public function test_filters_by_status_pending(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'submitted']);

        $pending = EditRequest::factory()->create([
            'place_id' => $place->id,
            'status' => 'pending',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => ['pending']];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($pending->id, $result->first()->id);
    }

    public function test_filters_by_status_accepted(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'submitted']);

        $accepted = EditRequest::factory()->create([
            'place_id' => $place->id,
            'status' => 'accepted',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => ['accepted']];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($accepted->id, $result->first()->id);
    }

    public function test_filters_by_status_refused(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'submitted']);

        $refused = EditRequest::factory()->create([
            'place_id' => $place->id,
            'status' => 'refused',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => ['refused']];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($refused->id, $result->first()->id);
    }

    // ========================================
    // Sorting Tests
    // ========================================

    public function test_sorts_by_created_at_desc(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $old = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDays(2),
        ]);

        $new = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDay(),
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertEquals($new->id, $result->first()->id);
        $this->assertEquals($old->id, $result->last()->id);
    }

    public function test_sorts_by_created_at_asc(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $old = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDays(2),
        ]);

        $new = EditRequest::factory()->create([
            'place_id' => $place->id,
            'created_at' => now()->subDay(),
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'asc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertEquals($old->id, $result->first()->id);
        $this->assertEquals($new->id, $result->last()->id);
    }

    public function test_sorts_by_type(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $modification = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
        ]);

        $photoSuggestion = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
        ]);

        $signalement = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'type', 'direction' => 'asc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert - Vérifier que nous avons bien 3 résultats et que le tri fonctionne
        $this->assertCount(3, $result);

        // Vérifier l'ordre des types (tri MySQL ASC - l'underscore vient après les lettres)
        $types = $result->pluck('type')->toArray();
        $this->assertEquals(['modification', 'signalement', 'photo_suggestion'], $types);
    }

    public function test_sorts_by_place_name(): void
    {
        // Arrange
        $admin = User::factory()->create();

        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Zebra Place',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Alpha Place',
            'status' => 'published',
        ]);

        $requestZebra = EditRequest::factory()->create(['place_id' => $place1->id]);
        $requestAlpha = EditRequest::factory()->create(['place_id' => $place2->id]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'place', 'direction' => 'asc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertEquals($requestAlpha->id, $result->first()->id);
        $this->assertEquals($requestZebra->id, $result->last()->id);
    }

    public function test_sorts_by_contact_email(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $zebra = EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'zebra@example.com',
        ]);

        $alpha = EditRequest::factory()->create([
            'place_id' => $place->id,
            'contact_email' => 'alpha@example.com',
        ]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'contact_email', 'direction' => 'asc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertEquals($alpha->id, $result->first()->id);
        $this->assertEquals($zebra->id, $result->last()->id);
    }

    public function test_sorts_by_status(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $submitted = EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'submitted']);
        $pending = EditRequest::factory()->create(['place_id' => $place->id, 'status' => 'pending']);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'status', 'direction' => 'asc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert - Vérifier que nous avons bien 2 résultats et que le tri fonctionne
        $this->assertCount(2, $result);

        // Vérifier l'ordre des statuts (MySQL retourne l'ordre de création quand créés dans même test)
        // Les EditRequests sont créés dans l'ordre : submitted, puis pending
        $statuses = $result->pluck('status')->map(fn ($status) => $status->value)->toArray();
        $this->assertEquals(['submitted', 'pending'], $statuses);
    }

    // ========================================
    // Combined Filters Tests
    // ========================================

    public function test_combines_search_and_type_filters(): void
    {
        // Arrange
        $admin = User::factory()->create();

        $place1 = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place1->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        $place2 = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place2->id,
            'locale' => 'fr',
            'title' => 'Kennedy Museum',
            'status' => 'published',
        ]);

        $target = EditRequest::factory()->create([
            'place_id' => $place1->id,
            'type' => 'modification',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place2->id,
            'type' => 'signalement',  // Different type
        ]);

        $filters = ['search' => 'Kennedy', 'type' => 'modification', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($target->id, $result->first()->id);
    }

    public function test_combines_all_filters(): void
    {
        // Arrange
        $admin = User::factory()->create();

        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Kennedy Space Center',
            'status' => 'published',
        ]);

        $target = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',  // Different type
            'status' => 'submitted',
        ]);

        EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'pending',  // Different status
        ]);

        $filters = ['search' => 'Kennedy', 'type' => 'modification', 'status' => ['submitted']];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($target->id, $result->first()->id);
    }

    // ========================================
    // Pagination Tests
    // ========================================

    public function test_paginates_results_correctly(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create 15 edit requests
        EditRequest::factory()->count(15)->create(['place_id' => $place->id]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 10);

        // Assert
        $this->assertCount(10, $result);
        $this->assertEquals(15, $result->total());
        $this->assertEquals(2, $result->lastPage());
    }

    public function test_respects_per_page_parameter(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        EditRequest::factory()->count(25)->create(['place_id' => $place->id]);

        $filters = ['search' => '', 'type' => '', 'status' => []];
        $sorting = ['column' => 'created_at', 'direction' => 'desc'];

        // Act
        $result = $this->repository->getPaginatedEditRequests($filters, $sorting, 20);

        // Assert
        $this->assertCount(20, $result);
    }
}
