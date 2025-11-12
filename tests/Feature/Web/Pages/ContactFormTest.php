<?php

namespace Tests\Feature\Web\Pages;

use App\Livewire\Web\Contact\ContactForm;
use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('contact-form:127.0.0.1');

        // Mock reCAPTCHA v3 API
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'contact_form_submit',
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'localhost',
            ], 200),
        ]);
    }

    public function test_contact_page_displays_correctly(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertStatus(200);
        $response->assertSee('Contactez-nous');
        $response->assertSeeLivewire(ContactForm::class);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', '')
            ->set('email', '')
            ->set('message', '')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_form_validates_email_format(): void
    {
        Livewire::test(ContactForm::class)
            ->set('email', 'invalid-email')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['email']);
    }

    public function test_contact_form_validates_minimum_message_length(): void
    {
        Livewire::test(ContactForm::class)
            ->set('message', 'Hi')
            ->call('submit', 'fake-recaptcha-token')
            ->assertHasErrors(['message']);
    }

    public function test_contact_form_sends_email_successfully(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean.dupont@example.com')
            ->set('subject', 'Test Subject')
            ->set('message', 'Ceci est un message de test')
            ->call('submit', 'fake-recaptcha-token')
            ->assertSet('success', true)
            ->assertSet('errorMessage', '');

        Mail::assertSent(ContactMessageMail::class, function ($mail) {
            return $mail->name === 'Jean Dupont'
                && $mail->email === 'jean.dupont@example.com'
                && $mail->messageSubject === 'Test Subject'
                && $mail->messageContent === 'Ceci est un message de test';
        });
    }

    public function test_contact_form_resets_after_successful_submission(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean.dupont@example.com')
            ->set('message', 'Message de test')
            ->call('submit', 'fake-recaptcha-token')
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('subject', '')
            ->assertSet('message', '');
    }

    public function test_contact_form_enforces_rate_limiting(): void
    {
        Mail::fake();

        $component = Livewire::test(ContactForm::class);

        // Premier envoi - succès
        $component
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean.dupont@example.com')
            ->set('message', 'Premier message')
            ->call('submit', 'fake-recaptcha-token')
            ->assertSet('success', true);

        // Deuxième envoi - succès
        $component
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean.dupont@example.com')
            ->set('message', 'Deuxième message')
            ->call('submit', 'fake-recaptcha-token')
            ->assertSet('success', true);

        // Troisième envoi - succès
        $component
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean.dupont@example.com')
            ->set('message', 'Troisième message')
            ->call('submit', 'fake-recaptcha-token')
            ->assertSet('success', true);

        // Quatrième envoi - rate limit atteint
        $component
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean.dupont@example.com')
            ->set('message', 'Quatrième message')
            ->call('submit', 'fake-recaptcha-token')
            ->assertSet('success', false)
            ->assertSet('errorMessage', __('web/pages/contact.form.errors.rate_limit'));
    }

    public function test_contact_page_contains_recaptcha_script(): void
    {
        $response = $this->get('/fr/contact');

        $response->assertSee('https://www.google.com/recaptcha/api.js', false);
    }
}
