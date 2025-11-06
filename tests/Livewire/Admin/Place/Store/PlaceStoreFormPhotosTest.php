<?php

namespace Tests\Livewire\Admin\Place\Store;

use App\Livewire\Admin\Place\Store\PlaceStoreForm;
use App\Models\Photo;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceStoreFormPhotosTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
        app()->setLocale('fr');

        Storage::fake('public');
    }

    // ========================================
    // Photo Management Tests
    // ========================================

    public function test_edit_mode_loads_existing_photos(): void
    {
        $place = Place::factory()->create();

        Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo1.jpg',
            'is_main' => true,
            'sort_order' => 1,
        ]);

        Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo2.jpg',
            'is_main' => false,
            'sort_order' => 2,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null]);

        $existingPhotos = $component->get('existingPhotos');

        $this->assertCount(2, $existingPhotos);
        $this->assertTrue($existingPhotos[0]['is_main']);
        $this->assertFalse($existingPhotos[1]['is_main']);
    }

    public function test_set_main_photo_updates_main_photo_id(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo1.jpg',
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $photo2 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo2.jpg',
            'is_main' => false,
            'sort_order' => 1,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('setMainPhoto', $photo2->id);

        // Vérifier que mainPhotoId a été mis à jour
        $this->assertEquals($photo2->id, $component->get('mainPhotoId'));

        // Vérifier que photo2 est maintenant en première position
        $existingPhotos = $component->get('existingPhotos');
        $this->assertEquals($photo2->id, $existingPhotos[0]['id']);
    }

    public function test_delete_photo_removes_from_existing_photos(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo1.jpg',
            'sort_order' => 1,
        ]);

        $photo2 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo2.jpg',
            'sort_order' => 2,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('deletePhoto', $photo1->id);

        $existingPhotos = $component->get('existingPhotos');

        $this->assertCount(1, $existingPhotos);
        $this->assertEquals($photo2->id, $existingPhotos[0]['id']);
    }

    public function test_deleting_main_photo_sets_next_photo_as_main(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo1.jpg',
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $photo2 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo2.jpg',
            'is_main' => false,
            'sort_order' => 1,
        ]);

        $photo3 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo3.jpg',
            'is_main' => false,
            'sort_order' => 2,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('deletePhoto', $photo1->id);

        // Photo2 devrait devenir principale (première du tableau réindexé)
        $this->assertEquals($photo2->id, $component->get('mainPhotoId'));

        $existingPhotos = $component->get('existingPhotos');
        $this->assertCount(2, $existingPhotos);
        $this->assertEquals($photo2->id, $existingPhotos[0]['id']);
    }

    public function test_deleting_non_main_photo_keeps_main_photo_unchanged(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo1.jpg',
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $photo2 = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo2.jpg',
            'is_main' => false,
            'sort_order' => 1,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('deletePhoto', $photo2->id);

        // Photo1 doit rester principale
        $this->assertEquals($photo1->id, $component->get('mainPhotoId'));

        $existingPhotos = $component->get('existingPhotos');
        $this->assertCount(1, $existingPhotos);
    }

    public function test_deleting_last_photo_sets_main_photo_to_null(): void
    {
        $place = Place::factory()->create();

        $photo = Photo::factory()->create([
            'place_id' => $place->id,
            'filename' => 'photo.jpg',
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('deletePhoto', $photo->id);

        $this->assertNull($component->get('mainPhotoId'));
        $this->assertEmpty($component->get('existingPhotos'));
    }

    public function test_existing_photos_are_reindexed_after_deletion(): void
    {
        $place = Place::factory()->create();

        Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 0]);
        $photo2 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 1]);
        $photo3 = Photo::factory()->create(['place_id' => $place->id, 'sort_order' => 2]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null]);

        $existingPhotos = $component->get('existingPhotos');
        $firstPhotoId = $existingPhotos[0]['id'];

        $component->call('deletePhoto', $firstPhotoId);

        $existingPhotos = $component->get('existingPhotos');

        // Vérifier que les indices sont bien 0, 1 (pas 1, 2)
        $this->assertArrayHasKey(0, $existingPhotos);
        $this->assertArrayHasKey(1, $existingPhotos);
        $this->assertEquals($photo2->id, $existingPhotos[0]['id']);
        $this->assertEquals($photo3->id, $existingPhotos[1]['id']);
    }

    public function test_set_main_photo_swaps_sort_order_with_first_photo(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $photo2 = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'sort_order' => 1,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('setMainPhoto', $photo2->id);

        $photoOrder = $component->get('photoOrder');

        // Vérifier que les sort_order ont été échangés
        $this->assertEquals(1, $photoOrder[$photo1->id]);
        $this->assertEquals(0, $photoOrder[$photo2->id]);

        // Vérifier que photo2 est maintenant principale
        $this->assertEquals($photo2->id, $component->get('mainPhotoId'));
    }

    public function test_update_photo_order_updates_main_photo_if_first_position_changes(): void
    {
        $place = Place::factory()->create();

        $photo1 = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => true,
            'sort_order' => 0,
        ]);

        $photo2 = Photo::factory()->create([
            'place_id' => $place->id,
            'is_main' => false,
            'sort_order' => 1,
        ]);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => $place->id, 'placeRequestId' => null])
            ->call('updatePhotoOrder', [
                $photo1->id => 1,  // Photo1 passe en 2e position
                $photo2->id => 0,  // Photo2 passe en 1ère position
            ]);

        // Photo2 devrait devenir principale car sort_order = 0
        $this->assertEquals($photo2->id, $component->get('mainPhotoId'));
    }

    public function test_remove_photo_removes_from_pending_photos_array(): void
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->image('photo1.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('photo2.jpg', 800, 600);

        $component = Livewire::test(PlaceStoreForm::class, ['placeId' => null, 'placeRequestId' => null])
            ->set('pendingPhotos', [$file1, $file2]);

        // La validation automatique se déclenche via le lifecycle hook updatedPendingPhotos()
        // qui est appelé automatiquement par Livewire lors du set() ci-dessus

        $photos = $component->get('photos');
        $this->assertCount(2, $photos);

        // Supprimer la première photo
        $component->call('removePhoto', 0);

        $photos = $component->get('photos');
        $this->assertCount(1, $photos);
    }
}
