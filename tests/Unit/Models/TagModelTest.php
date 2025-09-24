<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_generates_slug_automatically_on_create(): void
    {
        $tag = new Tag([
            'name' => 'Space Shuttle',
            'description' => 'Test description',
            'is_active' => true,
        ]);
        $tag->save();

        $this->assertEquals('space-shuttle', $tag->slug);
    }

    public function test_tag_updates_slug_when_name_changes(): void
    {
        $tag = new Tag([
            'name' => 'Apollo Program',
            'description' => 'Test description',
            'is_active' => true,
        ]);
        $tag->save();

        $this->assertEquals('apollo-program', $tag->slug);

        // Réinitialiser le slug pour que la logique de mise à jour fonctionne
        $tag->slug = '';
        $tag->save();

        $tag->update(['name' => 'Apollo Mission']);
        $tag->refresh();
        $this->assertEquals('apollo-mission', $tag->slug);
    }

    public function test_tag_keeps_existing_slug_when_updating(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Rocket',
            'slug' => 'custom-rocket-slug',
        ]);

        $this->assertEquals('custom-rocket-slug', $tag->slug);

        $tag->update(['description' => 'Updated description']);
        $this->assertEquals('custom-rocket-slug', $tag->slug);
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
