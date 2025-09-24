<?php

namespace Tests\Unit\Models;

use App\Models\Photo;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_has_place_relation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        $photo = Photo::factory()->create(['place_id' => $place->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $photo->place());
        $this->assertEquals($place->id, $photo->place->id);
    }

    public function test_photo_url_attribute(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        $photo = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'test-image.jpg',
        ]);

        $expectedUrl = asset('storage/photos/test-image.jpg');
        $this->assertEquals($expectedUrl, $photo->url);
    }

    public function test_photo_thumb_url_attribute(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        $photo = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'test-image.jpg',
        ]);

        $expectedUrl = asset('storage/photos/thumbs/test-image_thumb.jpg');
        $this->assertEquals($expectedUrl, $photo->thumb_url);
    }

    public function test_photo_medium_url_attribute(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        $photo = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'test-image.jpg',
        ]);

        $expectedUrl = asset('storage/photos/medium/test-image_medium.jpg');
        $this->assertEquals($expectedUrl, $photo->medium_url);
    }

    public function test_photo_casts_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);
        $photo = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'sort_order' => 1,
            'size' => 1024,
        ]);

        $this->assertIsBool($photo->is_main);
        $this->assertIsInt($photo->sort_order);
        $this->assertIsInt($photo->size);
    }
}
