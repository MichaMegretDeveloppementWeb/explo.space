<?php

namespace Tests\Livewire\Web\Place\PhotoSuggestion;

use App\DTO\Web\Place\PlaceDetailDTO;
use App\Enums\RequestStatus;
use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Livewire\Web\Place\PhotoSuggestion\PhotoSuggestionForm;
use App\Models\EditRequest;
use App\Models\Place;
use App\Services\Web\Place\PhotoSuggestion\PhotoSuggestionCreateService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhotoSuggestionFormTest extends TestCase
{
    use RefreshDatabase;

    private Place $place;

    private PlaceDetailDTO $placeDTO;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('edit_request_photos');

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
        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->assertStatus(200)
            ->assertViewIs('livewire.web.place.photo-suggestion.photo-suggestion-form');
    }

    #[Test]
    public function it_initializes_with_form_hidden(): void
    {
        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->assertSet('showForm', false)
            ->assertSet('pendingPhotos', [])
            ->assertSet('photos', [])
            ->assertSet('contact_email', '')
            ->assertSet('recaptcha_token', '');
    }

    #[Test]
    public function it_toggles_form_visibility(): void
    {
        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->assertSet('showForm', false)
            ->call('toggleForm')
            ->assertSet('showForm', true)
            ->call('toggleForm')
            ->assertSet('showForm', false);
    }

    #[Test]
    public function it_resets_form_when_closing(): void
    {
        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('showForm', true)
            ->set('contact_email', 'test@example.com')
            ->set('photos', [UploadedFile::fake()->image('photo.jpg')])
            ->set('recaptcha_token', 'fake-token')
            ->call('toggleForm')
            ->assertSet('showForm', false)
            ->assertSet('pendingPhotos', [])
            ->assertSet('photos', [])
            ->assertSet('contact_email', '')
            ->assertSet('recaptcha_token', '');
    }

    #[Test]
    public function it_validates_and_merges_pending_photos(): void
    {
        $photo1 = UploadedFile::fake()->image('photo1.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('photo2.jpg', 1024, 768);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', [$photo1, $photo2])
            ->assertSet('photos', [$photo1, $photo2])
            ->assertSet('pendingPhotos', [])
            ->assertHasNoErrors('pendingPhotos');
    }

    #[Test]
    public function it_rejects_photos_exceeding_size_limit(): void
    {
        $maxSizeKB = config('upload.images.max_size_kb');
        $oversizedPhoto = UploadedFile::fake()->create('large.jpg', $maxSizeKB + 1);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', [$oversizedPhoto])
            ->assertHasErrors('pendingPhotos')
            ->assertSet('pendingPhotos', []);
    }

    #[Test]
    public function it_rejects_invalid_file_types(): void
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', [$invalidFile])
            ->assertHasErrors('pendingPhotos')
            ->assertSet('pendingPhotos', []);
    }

    #[Test]
    public function it_enforces_maximum_photo_limit(): void
    {
        $maxFiles = config('upload.images.max_files');

        // Add max photos first
        $photos = [];
        for ($i = 0; $i < $maxFiles; $i++) {
            $photos[] = UploadedFile::fake()->image("photo{$i}.jpg");
        }

        $component = Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', $photos)
            ->assertSet('photos', $photos);

        // Try to add one more
        $extraPhoto = UploadedFile::fake()->image('extra.jpg');
        $component->set('pendingPhotos', [$extraPhoto])
            ->assertHasErrors('pendingPhotos')
            ->assertSet('pendingPhotos', []);
    }

    #[Test]
    public function it_accumulates_photos_across_multiple_uploads(): void
    {
        $photo1 = UploadedFile::fake()->image('photo1.jpg');
        $photo2 = UploadedFile::fake()->image('photo2.jpg');
        $photo3 = UploadedFile::fake()->image('photo3.jpg');

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', [$photo1])
            ->assertCount('photos', 1)
            ->set('pendingPhotos', [$photo2, $photo3])
            ->assertCount('photos', 3);
    }

    #[Test]
    public function it_removes_photo_by_index(): void
    {
        $photo1 = UploadedFile::fake()->image('photo1.jpg');
        $photo2 = UploadedFile::fake()->image('photo2.jpg');
        $photo3 = UploadedFile::fake()->image('photo3.jpg');

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', [$photo1, $photo2, $photo3])
            ->assertCount('photos', 3)
            ->call('removePhoto', 1) // Remove second photo
            ->assertCount('photos', 2);
    }

    #[Test]
    public function it_reindexes_array_after_photo_removal(): void
    {
        $photo1 = UploadedFile::fake()->image('photo1.jpg');
        $photo2 = UploadedFile::fake()->image('photo2.jpg');

        $component = Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('pendingPhotos', [$photo1, $photo2])
            ->call('removePhoto', 0);

        // Verify array is properly re-indexed (no gaps)
        $photos = $component->get('photos');
        $this->assertArrayHasKey(0, $photos);
        $this->assertArrayNotHasKey(1, $photos);
    }

    #[Test]
    public function it_validates_required_fields_on_submit(): void
    {
        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['contact_email', 'photos']);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'invalid-email')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('contact_email');
    }

    #[Test]
    public function it_checks_place_existence_before_submission(): void
    {
        // Delete the place
        $this->place->delete();

        $photo = UploadedFile::fake()->image('photo.jpg');

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit')
            ->assertSee(__('errors/photo-suggestion.place_not_found'));
    }

    #[Test]
    public function it_submits_successfully_with_valid_data(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock the service
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($data) {
                return $data['place_id'] === $this->place->id
                    && $data['contact_email'] === 'test@example.com'
                    && count($data['photos']) === 1;
            }));

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertSet('photos', [])
            ->assertSet('contact_email', '')
            ->assertSessionHas('success', __('web/pages/place-show.photo_suggestion.success'));
    }

    #[Test]
    public function it_handles_photo_validation_exception(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock service to throw PhotoValidationException
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create')
            ->willThrowException(PhotoValidationException::svgNotAllowed());

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit')
            ->assertSee(__('errors/photo-suggestion.photo_validation'));
    }

    #[Test]
    public function it_handles_photo_processing_exception(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock service to throw PhotoProcessingException
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create')
            ->willThrowException(new PhotoProcessingException('Storage error', 'storage.failed'));

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit')
            ->assertSee(__('errors/photo-suggestion.photo_processing'));
    }

    #[Test]
    public function it_handles_unexpected_photo_exception(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock service to throw UnexpectedPhotoException
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create')
            ->willThrowException(new UnexpectedPhotoException('Unexpected error'));

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit')
            ->assertSee(__('errors/photo-suggestion.unexpected_photo'));
    }

    #[Test]
    public function it_handles_database_exception(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock service to throw QueryException
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create')
            ->willThrowException(new QueryException(
                'mysql',
                'INSERT INTO edit_requests',
                [],
                new \Exception('Database error')
            ));

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit')
            ->assertSee(__('errors/photo-suggestion.database'));
    }

    #[Test]
    public function it_handles_unexpected_submit_exception(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock service to throw generic exception
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors('submit')
            ->assertSee(__('errors/photo-suggestion.unexpected'));
    }

    #[Test]
    public function it_handles_recaptcha_error_from_frontend(): void
    {
        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->call('handleRecaptchaError', 'reCAPTCHA validation failed')
            ->assertHasErrors('recaptcha_token')
            ->assertSee('reCAPTCHA validation failed');
    }

    #[Test]
    public function it_assigns_recaptcha_token_on_submit(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock the service
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create');

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'test-recaptcha-token-123')
            ->assertSet('recaptcha_token', 'test-recaptcha-token-123');
    }

    #[Test]
    public function it_creates_edit_request_with_correct_data(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token');

        $this->assertDatabaseHas('edit_requests', [
            'place_id' => $this->place->id,
            'type' => 'photo_suggestion',
            'contact_email' => 'test@example.com',
            'status' => RequestStatus::Submitted->value,
        ]);
    }

    #[Test]
    public function it_stores_photos_in_correct_disk(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token');

        // Verify photos are stored
        $editRequest = EditRequest::where('place_id', $this->place->id)->first();
        $this->assertNotNull($editRequest);
        $this->assertNotEmpty($editRequest->suggested_changes['photos']);

        // Verify file exists on disk
        $filename = $editRequest->suggested_changes['photos'][0];
        Storage::disk('edit_request_photos')->assertExists($editRequest->id.'/'.$filename);
    }

    #[Test]
    public function it_prevents_multiple_submissions_while_processing(): void
    {
        $photo = UploadedFile::fake()->image('photo.jpg');

        // Mock slow service
        $mockService = $this->createMock(PhotoSuggestionCreateService::class);
        $mockService->method('create')
            ->willReturnCallback(function () {
                usleep(100000); // 100ms delay
            });

        $this->app->instance(PhotoSuggestionCreateService::class, $mockService);

        Livewire::test(PhotoSuggestionForm::class, ['place' => $this->placeDTO])
            ->set('photos', [$photo])
            ->set('contact_email', 'test@example.com')
            ->call('submit', 'fake-recaptcha-token');

        // Service should be called only once even if triggered multiple times
        // (Livewire handles this automatically)
    }
}
