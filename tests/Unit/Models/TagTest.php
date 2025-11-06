<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_translation_generates_slug_automatically_on_create(): void
    {
        $tag = Tag::factory()->create();

        $translation = new TagTranslation([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Space Shuttle',
            'description' => 'Test description',
        ]);
        $translation->save();

        $this->assertEquals('space-shuttle', $translation->slug);
    }

    public function test_tag_translation_updates_slug_when_name_changes(): void
    {
        $tag = Tag::factory()->create();

        $translation = TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Apollo Program',
            'description' => 'Test description',
            'slug' => '', // Force empty slug to trigger auto-generation
        ]);

        $this->assertEquals('apollo-program', $translation->slug);

        // Le slug ne devrait PAS changer car il existe déjà (pas vide à l'origine)
        $translation->update(['name' => 'Apollo Mission']);
        $translation->refresh();
        $this->assertEquals('apollo-program', $translation->slug); // Garde l'ancien slug
    }

    public function test_tag_translation_keeps_existing_slug_when_updating(): void
    {
        $tag = Tag::factory()->create();

        $translation = TagTranslation::factory()->create([
            'tag_id' => $tag->id,
            'locale' => 'fr',
            'name' => 'Rocket',
            'slug' => 'custom-rocket-slug',
        ]);

        $this->assertEquals('custom-rocket-slug', $translation->slug);

        $translation->update(['description' => 'Updated description']);
        $this->assertEquals('custom-rocket-slug', $translation->slug);
    }

    public function test_tag_has_places_relation(): void
    {
        $tag = Tag::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $tag->places());
    }

    public function test_tag_casts_is_active_as_boolean(): void
    {
        $tag = Tag::factory()->create(['is_active' => true]);

        $this->assertIsBool($tag->is_active);
        $this->assertTrue($tag->is_active);
    }
}
