<?php

namespace Tests\Unit\Models;

use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_generates_slug_automatically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = new Place([
            'title' => 'Kennedy Space Center',
            'description' => 'Test description',
            'latitude' => 28.5721022,
            'longitude' => -80.6480131,
            'address' => 'Test address',
            'admin_id' => $admin->id,
        ]);
        $place->save();

        $this->assertEquals('kennedy-space-center', $place->slug);
    }

    public function test_place_has_required_relations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create(['admin_id' => $admin->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $place->admin());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $place->photos());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $place->tags());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $place->categories());
    }

    public function test_place_casts_coordinates_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create([
            'latitude' => 28.5721022,
            'longitude' => -80.6480131,
            'admin_id' => $admin->id,
        ]);

        $place->refresh();
        $this->assertIsNumeric($place->latitude);
        $this->assertIsNumeric($place->longitude);
        $this->assertEquals('28.5721022', (string) $place->latitude);
        $this->assertEquals('-80.6480131', (string) $place->longitude);
    }

    public function test_place_casts_is_featured_as_boolean(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $place = Place::factory()->create([
            'is_featured' => true,
            'admin_id' => $admin->id,
        ]);

        $this->assertIsBool($place->is_featured);
        $this->assertTrue($place->is_featured);
    }
}
