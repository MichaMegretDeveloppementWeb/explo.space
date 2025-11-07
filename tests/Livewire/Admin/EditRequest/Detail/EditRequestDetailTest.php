<?php

namespace Tests\Livewire\Admin\EditRequest\Detail;

use App\Livewire\Admin\EditRequest\Detail\EditRequestDetail;
use App\Models\EditRequest;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditRequestDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->admin);
    }

    // ========================================
    // Tests pour applyModification (selected_fields)
    // ========================================

    public function test_apply_modification_redirects_with_selected_fields(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('selectedFields', ['title', 'description'])
            ->call('applyModification')
            ->assertRedirect(route('admin.places.edit', [
                'id' => $place->id,
                'edit_request_id' => $editRequest->id,
                'selected_fields' => ['title', 'description'],
            ]));
    }

    public function test_apply_modification_shows_error_when_no_fields_selected(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('selectedFields', [])
            ->call('applyModification')
            ->assertHasNoErrors()
            ->assertNoRedirect()
            ->assertSessionHas('error', 'Veuillez sélectionner au moins un champ à appliquer.');
    }

    public function test_apply_modification_shows_error_when_not_modification_type(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('selectedFields', ['title'])
            ->call('applyModification')
            ->assertHasNoErrors()
            ->assertNoRedirect()
            ->assertSessionHas('error', 'Cette action n\'est disponible que pour les propositions de modification.');
    }

    // ========================================
    // Tests pour applyPhotoSuggestion (selected_photos)
    // ========================================

    public function test_apply_photo_suggestion_redirects_with_selected_photos(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('selectedPhotos', [0, 1, 2])
            ->call('applyPhotoSuggestion')
            ->assertRedirect(route('admin.places.edit', [
                'id' => $place->id,
                'edit_request_id' => $editRequest->id,
                'selected_photos' => [0, 1, 2],
            ]));
    }

    public function test_apply_photo_suggestion_shows_error_when_no_photos_selected(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'photo_suggestion',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('selectedPhotos', [])
            ->call('applyPhotoSuggestion')
            ->assertHasNoErrors()
            ->assertNoRedirect()
            ->assertSessionHas('error', 'Veuillez sélectionner au moins une photo à appliquer.');
    }

    public function test_apply_photo_suggestion_shows_error_when_not_photo_suggestion_type(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('selectedPhotos', [0])
            ->call('applyPhotoSuggestion')
            ->assertHasNoErrors()
            ->assertNoRedirect()
            ->assertSessionHas('error', 'Cette action n\'est disponible que pour les propositions de photos.');
    }

    // ========================================
    // Tests pour refusalEditRequest
    // ========================================

    public function test_refuse_edit_request_updates_status_and_refreshes_data(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('refusalReason', 'Invalid data')
            ->call('refuseEditRequest')
            ->assertHasNoErrors()
            ->assertSessionHas('success', 'La demande a été refusée avec succès.');

        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Refused, $editRequest->status);
        $this->assertEquals('Invalid data', $editRequest->reason);
        $this->assertEquals($this->admin->id, $editRequest->processed_by_admin_id);
        $this->assertNotNull($editRequest->processed_at);
    }

    public function test_refuse_edit_request_shows_error_when_already_processed(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'accepted', // Already processed
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('refusalReason', 'Invalid data')
            ->call('refuseEditRequest')
            ->assertSessionHas('error', 'Cette demande ne peut pas être refusée.');
    }

    // ========================================
    // Tests pour acceptSignalement
    // ========================================

    public function test_accept_signalement_updates_status(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'signalement',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->call('acceptSignalement')
            ->assertHasNoErrors()
            ->assertSessionHas('success', 'Le signalement a été marqué comme traité avec succès.');

        $editRequest->refresh();
        $this->assertEquals(\App\Enums\RequestStatus::Accepted, $editRequest->status);
        $this->assertEquals($this->admin->id, $editRequest->processed_by_admin_id);
        $this->assertNotNull($editRequest->processed_at);
    }

    public function test_accept_signalement_shows_error_when_not_signalement_type(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->call('acceptSignalement')
            ->assertSessionHas('error', 'Cette action n\'est disponible que pour les signalements simples.');
    }

    // ========================================
    // Tests pour refusal modal
    // ========================================

    public function test_open_refusal_modal_shows_modal(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->assertSet('showRefusalModal', false)
            ->call('openRefusalModal')
            ->assertSet('showRefusalModal', true)
            ->assertSet('refusalReason', null);
    }

    public function test_close_refusal_modal_hides_modal(): void
    {
        $place = Place::factory()
            ->hasTranslations(1, ['locale' => 'fr'])
            ->create();
        $editRequest = EditRequest::factory()->create([
            'place_id' => $place->id,
            'type' => 'modification',
            'status' => 'submitted',
        ]);

        $editRequest->load('place');

        Livewire::test(EditRequestDetail::class, ['editRequest' => $editRequest])
            ->set('showRefusalModal', true)
            ->set('refusalReason', 'Some reason')
            ->call('closeRefusalModal')
            ->assertSet('showRefusalModal', false)
            ->assertSet('refusalReason', null);
    }
}
