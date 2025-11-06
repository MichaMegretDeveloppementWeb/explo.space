<?php

namespace Tests\Livewire\Web\Place\PlaceRequest;

use App\Contracts\Translation\TranslationStrategyInterface;
use App\Livewire\Web\Place\PlaceRequest\PlaceRequestForm;
use App\Models\PlaceRequest;
use App\Services\Web\Place\Request\PlaceRequestCreateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PlaceRequestFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        // Mock Translation Strategy to avoid API calls
        $this->mock(TranslationStrategyInterface::class, function ($mock) {
            $mock->shouldReceive('detectLanguage')->andReturn('fr');
        });
    }

    public function test_it_renders_successfully(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->assertStatus(200);
    }

    public function test_it_validates_required_fields(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors([
                'contact_email' => 'required',
                'title' => 'required',
            ]);
    }

    public function test_it_validates_email_format(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'invalid-email')
            ->set('title', 'Test Title')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['contact_email' => 'email']);
    }

    public function test_it_validates_title_minimum_length(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'ABC') // Less than 7 characters
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['title' => 'min']);
    }

    public function test_it_validates_title_maximum_length(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', str_repeat('A', 256)) // More than 255 characters
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['title' => 'max']);
    }

    public function test_it_validates_description_maximum_length(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Valid Title')
            ->set('description', str_repeat('A', 5001)) // More than 5000
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['description' => 'max']);
    }

    public function test_it_validates_practical_info_maximum_length(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Valid Title')
            ->set('practical_info', str_repeat('A', 2001)) // More than 2000
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['practical_info' => 'max']);
    }

    public function test_it_validates_latitude_range(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Valid Title')
            ->set('latitude', 91) // Out of range
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['latitude' => 'between']);
    }

    public function test_it_validates_longitude_range(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Valid Title')
            ->set('longitude', 181) // Out of range
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['longitude' => 'between']);
    }

    public function test_it_dispatches_scroll_event_on_validation_failure(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->call('submit', 'fake-recaptcha-token')
            ->assertDispatched('scroll-to-validation-error');
    }

    public function test_it_creates_a_place_request_with_minimal_data(): void
    {
        // Arrange
        $this->mockRecaptcha();

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Centre Spatial Kennedy')
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertHasNoErrors()
            ->assertRedirect(route('home.fr'));

        // Assert
        $this->assertDatabaseHas('place_requests', [
            'contact_email' => 'test@example.com',
            'title' => 'Centre Spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
            'status' => 'submitted',
        ]);
    }

    public function test_it_creates_a_place_request_with_full_data(): void
    {
        // Arrange
        $this->mockRecaptcha();

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Centre Spatial Kennedy')
            ->set('description', 'Description du centre spatial')
            ->set('practical_info', 'Informations pratiques')
            ->set('latitude', 28.5721)
            ->set('longitude', -80.6480)
            ->set('address', 'Kennedy Space Center, FL, USA')
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertHasNoErrors()
            ->assertRedirect(route('home.fr'));

        // Assert
        $this->assertDatabaseHas('place_requests', [
            'contact_email' => 'test@example.com',
            'title' => 'Centre Spatial Kennedy',
            'description' => 'Description du centre spatial',
            'practical_info' => 'Informations pratiques',
            'latitude' => 28.5721,
            'longitude' => -80.6480,
            'address' => 'Kennedy Space Center, FL, USA',
            'status' => 'submitted',
        ]);
    }

    public function test_it_creates_a_place_request_with_photos(): void
    {
        // Arrange
        $this->mockRecaptcha();
        $photo1 = UploadedFile::fake()->image('photo1.jpg', 1000, 1000)->size(2000);
        $photo2 = UploadedFile::fake()->image('photo2.jpg', 1000, 1000)->size(1500);

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Test Place With Photos')
            ->set('photos', [$photo1, $photo2])
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertHasNoErrors();

        // Assert
        $placeRequest = PlaceRequest::where('title', 'Test Place With Photos')->first();
        $this->assertNotNull($placeRequest);
        $this->assertCount(2, $placeRequest->photos);
    }

    public function test_it_detects_language_correctly(): void
    {
        // Arrange
        $this->mockRecaptcha();
        $this->mock(TranslationStrategyInterface::class, function ($mock) {
            $mock->shouldReceive('detectLanguage')
                ->once()
                ->andReturn('en');
        });

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Kennedy Space Center')
            ->set('description', 'This is an English description')
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertHasNoErrors();

        // Assert
        $this->assertDatabaseHas('place_requests', [
            'title' => 'Kennedy Space Center',
            'detected_language' => 'en',
        ]);
    }

    public function test_it_resets_error_bag_on_submit(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->call('submit', 'fake-token')
            ->assertHasErrors(['contact_email', 'title'])
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Valid Title')
            ->call('submit', 'fake-token')
            // Errors should be reset before validation
            ->assertStatus(200);
    }

    public function test_it_shows_success_message_after_submission(): void
    {
        // Arrange
        $this->mockRecaptcha();

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Test Place')
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token');

        // Assert
        $this->assertEquals(
            __('web/pages/place-request.messages.success'),
            session('success')
        );
    }

    public function test_it_redirects_to_homepage_after_successful_submission(): void
    {
        // Arrange
        $this->mockRecaptcha();
        app()->setLocale('fr');

        // Act & Assert
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Test Place')
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertRedirect(route('home.fr'));
    }

    public function test_it_handles_photo_validation_exception(): void
    {
        // Arrange
        $this->mockRecaptcha();
        $this->mock(PlaceRequestCreateService::class, function ($mock) {
            $mock->shouldReceive('create')
                ->once()
                ->andThrow(new \App\Exceptions\Photo\PhotoValidationException(
                    'Photo too large',
                    'photo.size_limit'
                ));
        });

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Test Place')
            ->set('photos', [UploadedFile::fake()->image('large.jpg')])
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertHasErrors('photos');
    }

    public function test_it_handles_unexpected_exceptions_gracefully(): void
    {
        // Arrange
        $this->mockRecaptcha();
        $this->mock(PlaceRequestCreateService::class, function ($mock) {
            $mock->shouldReceive('create')
                ->once()
                ->andThrow(new \RuntimeException('Unexpected error'));
        });

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Test Place')
            ->set('recaptcha_token', 'valid-token')
            ->call('submit', 'valid-token')
            ->assertHasErrors('submit');
    }

    public function test_it_accepts_recaptcha_token_as_parameter(): void
    {
        // Arrange
        $this->mockRecaptcha();

        // Act
        Livewire::test(PlaceRequestForm::class)
            ->set('contact_email', 'test@example.com')
            ->set('title', 'Test Place')
            ->call('submit', 'token-from-javascript')
            ->assertHasNoErrors();

        // Assert
        $this->assertDatabaseHas('place_requests', [
            'title' => 'Test Place',
        ]);
    }

    public function test_it_initializes_with_empty_values(): void
    {
        Livewire::test(PlaceRequestForm::class)
            ->assertSet('contact_email', '')
            ->assertSet('title', '')
            ->assertSet('description', '')
            ->assertSet('practical_info', '')
            ->assertSet('latitude', null)
            ->assertSet('longitude', null)
            ->assertSet('address', null)
            ->assertSet('photos', [])
            ->assertSet('recaptcha_token', '');
    }

    public function test_it_provides_photo_validation_config_to_view(): void
    {
        $component = Livewire::test(PlaceRequestForm::class);

        $photoConfig = $component->viewData('photoConfig');

        $this->assertNotNull($photoConfig);
        $this->assertIsArray($photoConfig);
    }

    /**
     * Helper to mock reCAPTCHA validation
     */
    private function mockRecaptcha(): void
    {
        // Mock Google reCAPTCHA API response
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'submit',
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'localhost',
            ], 200),
        ]);
    }
}
