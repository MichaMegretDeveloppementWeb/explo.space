<?php

namespace Tests\Unit\Repositories\Web\Place\PreviewModal;

use App\DTO\Web\Place\PlacePreviewDTO;
use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;
use App\Models\Photo;
use App\Models\Place;
use App\Models\PlaceTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Repositories\Web\Place\PreviewModal\PlacePreviewRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacePreviewRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PlacePreviewRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PlacePreviewRepository;
        app()->setLocale('fr');
    }

    public function test_get_place_preview_by_id_returns_dto_with_all_data(): void
    {
        $place = Place::factory()->create([
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Tour Eiffel',
            'slug' => 'tour-eiffel',
            'description' => 'La Tour Eiffel est une tour de fer puddlé construite par Gustave Eiffel.',
            'status' => 'published',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'filename' => 'photo.jpg',
        ]);

        $tag = Tag::factory()->create(['color' => '#FF0000']);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Monument',
            'slug' => 'monument',
            'status' => 'published',
        ]);
        $place->tags()->attach($tag->id);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertInstanceOf(PlacePreviewDTO::class, $result);
        $this->assertEquals($place->id, $result->id);
        $this->assertEquals('tour-eiffel', $result->slug);
        $this->assertEquals('Tour Eiffel', $result->title);
        $this->assertNotEmpty($result->descriptionExcerpt);
        $this->assertNotNull($result->mainPhotoUrl);
        $this->assertCount(1, $result->tags);
        $this->assertEquals('Monument', $result->tags[0]['name']);
    }

    public function test_get_place_preview_by_id_throws_exception_when_place_not_found(): void
    {
        $this->expectException(PlaceNotFoundException::class);
        $this->expectExceptionMessage('Place with ID 999 not found');

        $this->repository->getPlacePreviewById(999);
    }

    public function test_get_place_preview_by_id_throws_exception_when_translation_not_found(): void
    {
        $place = Place::factory()->create();

        // Create translation in English only
        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'status' => 'published',
        ]);

        app()->setLocale('fr'); // But request in French

        $this->expectException(PlaceTranslationNotFoundException::class);
        $this->expectExceptionMessage('Translation for place ID');
        $this->expectExceptionMessage('not found for locale \'fr\'');

        $this->repository->getPlacePreviewById($place->id);
    }

    public function test_get_place_preview_by_id_ignores_draft_translations(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'draft', // Not published
        ]);

        $this->expectException(PlaceTranslationNotFoundException::class);

        $this->repository->getPlacePreviewById($place->id);
    }

    public function test_get_place_preview_by_id_handles_empty_description(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'description' => '', // Empty string instead of null (database constraint)
            'status' => 'published',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertEquals('', $result->descriptionExcerpt);
    }

    public function test_get_place_preview_by_id_truncates_long_description(): void
    {
        $place = Place::factory()->create();

        $longDescription = str_repeat('Lorem ipsum dolor sit amet. ', 50); // Very long text

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'description' => $longDescription,
            'status' => 'published',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertLessThanOrEqual(203, mb_strlen($result->descriptionExcerpt)); // 200 + "..."
        $this->assertStringEndsWith('...', $result->descriptionExcerpt);
    }

    public function test_get_place_preview_by_id_does_not_truncate_short_description(): void
    {
        $place = Place::factory()->create();

        $shortDescription = 'This is a short description.';

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'description' => $shortDescription,
            'status' => 'published',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertEquals($shortDescription, $result->descriptionExcerpt);
        $this->assertStringEndsNotWith('...', $result->descriptionExcerpt);
    }

    public function test_get_place_preview_by_id_cuts_at_last_space(): void
    {
        $place = Place::factory()->create();

        $description = str_repeat('word ', 50).'ending'; // Ensure it's > 200 chars

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'description' => $description,
            'status' => 'published',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        // Should not cut in the middle of "ending" word
        $this->assertStringEndsNotWith('e...', $result->descriptionExcerpt);
        $this->assertStringEndsNotWith('en...', $result->descriptionExcerpt);
    }

    public function test_get_place_preview_by_id_returns_null_photo_url_when_no_photo(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertNull($result->mainPhotoUrl);
    }

    public function test_get_place_preview_by_id_returns_main_photo_url(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'filename' => 'secondary.jpg',
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'filename' => 'main.jpg',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertNotNull($result->mainPhotoUrl);
        $this->assertStringContainsString('main.jpg', $result->mainPhotoUrl);
    }

    public function test_get_place_preview_by_id_limits_tags_to_five(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Create 10 tags
        for ($i = 1; $i <= 10; $i++) {
            $tag = Tag::factory()->create(['color' => '#FF0000']);
            TagTranslation::factory()->create([
                'tag_id' => $tag->id,
                'locale' => 'fr',
                'name' => "Tag {$i}",
                'slug' => "tag-{$i}",
                'status' => 'published',
            ]);
            $place->tags()->attach($tag->id);
        }

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertCount(5, $result->tags);
    }

    public function test_get_place_preview_by_id_excludes_tags_without_translation(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        // Tag with French translation
        $tagWithTranslation = Tag::factory()->create(['color' => '#FF0000']);
        TagTranslation::factory()->create([
            'tag_id' => $tagWithTranslation->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'status' => 'published',
        ]);
        $place->tags()->attach($tagWithTranslation->id);

        // Tag without French translation
        $tagWithoutTranslation = Tag::factory()->create(['color' => '#0000FF']);
        TagTranslation::factory()->create([
            'tag_id' => $tagWithoutTranslation->id,
            'locale' => 'en', // Only English
            'name' => 'SpaceX',
            'status' => 'published',
        ]);
        $place->tags()->attach($tagWithoutTranslation->id);

        app()->setLocale('fr');
        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertCount(1, $result->tags);
        $this->assertEquals('NASA', $result->tags[0]['name']);
    }

    public function test_get_place_preview_by_id_excludes_draft_tag_translations(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $tag = Tag::factory()->create(['color' => '#FF0000']);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'status' => 'draft', // Not published
        ]);
        $place->tags()->attach($tag->id);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertEmpty($result->tags);
    }

    public function test_get_place_preview_by_id_returns_empty_tags_array_when_no_tags(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertEmpty($result->tags);
        $this->assertIsArray($result->tags);
    }

    public function test_get_place_preview_by_id_uses_current_locale(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Titre en français',
            'slug' => 'titre-francais',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Title in English',
            'slug' => 'title-english',
            'status' => 'published',
        ]);

        app()->setLocale('fr');
        $resultFr = $this->repository->getPlacePreviewById($place->id);
        $this->assertEquals('Titre en français', $resultFr->title);

        app()->setLocale('en');
        $resultEn = $this->repository->getPlacePreviewById($place->id);
        $this->assertEquals('Title in English', $resultEn->title);
    }

    public function test_get_place_preview_by_id_tag_array_has_correct_structure(): void
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'status' => 'published',
        ]);

        $tag = Tag::factory()->create(['color' => '#FF5733']);
        TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'NASA',
            'slug' => 'nasa',
            'status' => 'published',
        ]);
        $place->tags()->attach($tag->id);

        $result = $this->repository->getPlacePreviewById($place->id);

        $this->assertCount(1, $result->tags);
        $this->assertArrayHasKey('name', $result->tags[0]);
        $this->assertArrayHasKey('slug', $result->tags[0]);
        $this->assertArrayHasKey('color', $result->tags[0]);
        $this->assertEquals('NASA', $result->tags[0]['name']);
        $this->assertEquals('nasa', $result->tags[0]['slug']);
        $this->assertEquals('#FF5733', $result->tags[0]['color']);
    }
}
