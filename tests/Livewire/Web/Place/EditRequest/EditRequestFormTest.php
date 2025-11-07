<?php

namespace Tests\Livewire\Web\Place\EditRequest;

use App\DTO\Web\Place\PlaceDetailDTO;
use App\Enums\RequestStatus;
use App\Livewire\Web\Place\EditRequest\EditRequestForm;
use App\Models\EditRequest;
use App\Models\Place;
use App\Services\Web\Place\EditRequest\EditRequestCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditRequestFormTest extends TestCase
{
    use RefreshDatabase;

    private Place $place;

    private PlaceDetailDTO $placeDTO;

    protected function setUp(): void
    {
        parent::setUp();

        $this->place = Place::factory()->create();
        $this->placeDTO = new PlaceDetailDTO(
            id: $this->place->id,
            slug: 'test-place',
            title: 'Test Place',
            description: 'Test description',
            practicalInfo: 'Test info',
            latitude: 48.8566,
            longitude: 2.3522,
            address: 'Paris, France',
            isFeatured: false,
            tags: [],
            photos: [],
            mainPhotoUrl: null,
            createdAt: now()->toDateTimeString(),
            updatedAt: now()->toDateTimeString()
        );
    }

    #[Test]
    public function it_renders_successfully(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->assertStatus(200)
            ->assertViewIs('livewire.web.place.edit-request.edit-request-form');
    }

    #[Test]
    public function it_initializes_with_form_hidden(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->assertSet('showForm', false)
            ->assertSet('type', 'signalement')
            ->assertSet('description', '')
            ->assertSet('contact_email', '')
            ->assertSet('selected_fields', [])
            ->assertSet('recaptcha_token', '');
    }

    #[Test]
    public function it_initializes_current_values_from_place(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->assertSet('current_values.title', 'Test Place')
            ->assertSet('current_values.description', 'Test description')
            ->assertSet('current_values.practical_info', 'Test info')
            ->assertSet('current_values.address', 'Paris, France')
            ->assertSet('current_values.coordinates.lat', 48.8566)
            ->assertSet('current_values.coordinates.lng', 2.3522);
    }

    #[Test]
    public function it_prefills_new_values_with_current_values(): void
    {
        $component = Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO]);

        $currentValues = $component->get('current_values');
        $newValues = $component->get('new_values');

        $this->assertEquals($currentValues, $newValues);
    }

    #[Test]
    public function it_opens_signalement_form(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->call('openSignalementForm')
            ->assertSet('showForm', true)
            ->assertSet('type', 'signalement');
    }

    #[Test]
    public function it_opens_modification_form(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->call('openModificationForm')
            ->assertSet('showForm', true)
            ->assertSet('type', 'modification');
    }

    #[Test]
    public function it_toggles_form_visibility(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->assertSet('showForm', false)
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function it_resets_form_when_closing(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('showForm', true)
            ->set('description', 'Test description')
            ->set('contact_email', 'test@example.com')
            ->set('selected_fields', ['title'])
            ->set('recaptcha_token', 'fake-token')
            ->call('toggleForm')
            ->assertSet('showForm', false)
            ->assertSet('description', '')
            ->assertSet('contact_email', '')
            ->assertSet('selected_fields', [])
            ->assertSet('recaptcha_token', '');
    }

    #[Test]
    public function it_restores_new_values_to_current_values_when_closing(): void
    {
        $component = Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('new_values.title', 'Modified Title')
            ->call('toggleForm');

        $currentValues = $component->get('current_values');
        $newValues = $component->get('new_values');

        $this->assertEquals($currentValues, $newValues);
        $this->assertEquals('Test Place', $newValues['title']);
    }

    #[Test]
    public function it_validates_required_fields_for_signalement(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['description', 'contact_email']);
    }

    #[Test]
    public function it_validates_required_fields_for_modification(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'modification')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['description', 'contact_email', 'selected_fields']);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->set('description', 'Test description')
            ->set('contact_email', 'invalid-email')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('contact_email');
    }

    #[Test]
    public function it_validates_selected_fields_for_modification(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'modification')
            ->set('description', 'Modifications')
            ->set('contact_email', 'test@example.com')
            ->set('selected_fields', [])
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('selected_fields');
    }

    #[Test]
    public function it_submits_signalement_successfully(): void
    {
        // Mock the service
        $mockService = $this->createMock(EditRequestCreateService::class);
        $mockService->expects($this->once())
            ->method('createEditRequest')
            ->with(
                $this->callback(function ($data) {
                    return $data['type'] === 'signalement'
                        && $data['description'] === 'Il y a une erreur'
                        && $data['contact_email'] === 'test@example.com';
                }),
                $this->placeDTO
            );

        $this->app->instance(EditRequestCreateService::class, $mockService);

        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->set('description', 'Il y a une erreur')
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertSet('description', '')
            ->assertSet('contact_email', '')
            ->assertSessionHas('success');
    }

    #[Test]
    public function it_submits_modification_successfully(): void
    {
        // Mock the service
        $mockService = $this->createMock(EditRequestCreateService::class);
        $mockService->expects($this->once())
            ->method('createEditRequest')
            ->with(
                $this->callback(function ($data) {
                    return $data['type'] === 'modification'
                        && $data['description'] === 'Voici mes modifications'
                        && $data['contact_email'] === 'modifier@example.com'
                        && $data['selected_fields'] === ['title', 'description']
                        && $data['new_values']['title'] === 'New Title';
                }),
                $this->placeDTO
            );

        $this->app->instance(EditRequestCreateService::class, $mockService);

        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'modification')
            ->set('description', 'Voici mes modifications')
            ->set('contact_email', 'modifier@example.com')
            ->set('selected_fields', ['title', 'description'])
            ->set('new_values.title', 'New Title')
            ->set('new_values.description', 'New Description')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertSessionHas('success');
    }

    #[Test]
    public function it_handles_service_exception(): void
    {
        // Mock service to throw exception
        $mockService = $this->createMock(EditRequestCreateService::class);
        $mockService->method('createEditRequest')
            ->willThrowException(new \RuntimeException('Service error'));

        $this->app->instance(EditRequestCreateService::class, $mockService);

        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->set('description', 'Test description')
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit');
    }

    #[Test]
    public function it_assigns_recaptcha_token_on_submit(): void
    {
        // Mock the service
        $mockService = $this->createMock(EditRequestCreateService::class);
        $mockService->method('createEditRequest');

        $this->app->instance(EditRequestCreateService::class, $mockService);

        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->set('description', 'Test')
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'test-recaptcha-token-123')
            ->assertSet('recaptcha_token', 'test-recaptcha-token-123');
    }

    #[Test]
    public function it_handles_recaptcha_error_from_frontend(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->call('handleRecaptchaError', 'reCAPTCHA validation failed')
            ->assertHasErrors('recaptcha_token')
            ->assertSee('reCAPTCHA validation failed');
    }

    #[Test]
    public function it_creates_edit_request_with_correct_data_in_database(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->set('description', 'Database test')
            ->set('contact_email', 'db@example.com')
            ->call('submit', 'fake-recaptcha-token');

        $this->assertDatabaseHas('edit_requests', [
            'place_id' => $this->place->id,
            'type' => 'signalement',
            'contact_email' => 'db@example.com',
            'description' => 'Database test',
            'status' => RequestStatus::Submitted->value,
        ]);
    }

    #[Test]
    public function it_creates_modification_with_suggested_changes_in_database(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'modification')
            ->set('description', 'Modification test')
            ->set('contact_email', 'mod@example.com')
            ->set('selected_fields', ['title'])
            ->set('new_values.title', 'Updated Title')
            ->call('submit', 'fake-recaptcha-token');

        $editRequest = EditRequest::where('place_id', $this->place->id)->first();

        $this->assertNotNull($editRequest);
        $this->assertEquals('modification', $editRequest->type);
        $this->assertIsArray($editRequest->suggested_changes);
        $this->assertCount(1, $editRequest->suggested_changes);
        $this->assertEquals('title', $editRequest->suggested_changes[0]['field']);
        $this->assertEquals('Test Place', $editRequest->suggested_changes[0]['old_value']);
        $this->assertEquals('Updated Title', $editRequest->suggested_changes[0]['new_value']);
    }

    #[Test]
    public function it_validates_coordinates_format_for_modification(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'modification')
            ->set('description', 'Coordinates test')
            ->set('contact_email', 'test@example.com')
            ->set('selected_fields', ['coordinates'])
            ->set('new_values.coordinates.lat', 'invalid')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('new_values.coordinates.lat');
    }

    #[Test]
    public function it_handles_multiple_field_modifications(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'modification')
            ->set('description', 'Multiple changes')
            ->set('contact_email', 'multi@example.com')
            ->set('selected_fields', ['title', 'description', 'address'])
            ->set('new_values.title', 'New Title')
            ->set('new_values.description', 'New Description')
            ->set('new_values.address', 'New Address')
            ->call('submit', 'fake-recaptcha-token');

        $editRequest = EditRequest::where('place_id', $this->place->id)->first();

        $this->assertNotNull($editRequest);
        $this->assertCount(3, $editRequest->suggested_changes);
    }

    #[Test]
    public function it_stores_detected_language(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->set('type', 'signalement')
            ->set('description', 'Il y a une erreur dans les informations')
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token');

        $editRequest = EditRequest::where('place_id', $this->place->id)->first();

        $this->assertNotNull($editRequest);
        $this->assertNotNull($editRequest->detected_language);
        $this->assertContains($editRequest->detected_language, ['fr', 'en', 'unknown']);
    }

    #[Test]
    public function it_resets_validation_when_opening_form(): void
    {
        Livewire::test(EditRequestForm::class, ['place' => $this->placeDTO])
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors()
            ->call('openSignalementForm')
            ->assertHasNoErrors();
    }
}
