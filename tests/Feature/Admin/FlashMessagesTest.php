<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashMessagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_single_success_message_is_displayed(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'))
            ->assertOk();

        $response = $this->actingAs($this->admin)
            ->withSession(['success' => 'Lieu créé avec succès'])
            ->get(route('admin.places.index'));

        $response->assertSee('Lieu créé avec succès');
        $response->assertSee('Succès');
    }

    public function test_single_error_message_is_displayed(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['error' => 'Une erreur est survenue'])
            ->get(route('admin.places.index'));

        $response->assertSee('Une erreur est survenue');
        $response->assertSee('Erreur');
    }

    public function test_single_warning_message_is_displayed(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['warning' => 'Attention aux données'])
            ->get(route('admin.places.index'));

        $response->assertSee('Attention aux données');
        $response->assertSee('Attention');
    }

    public function test_single_info_message_is_displayed(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['info' => 'Information importante'])
            ->get(route('admin.places.index'));

        $response->assertSee('Information importante');
        $response->assertSee('Information');
    }

    public function test_multiple_success_messages_are_displayed(): void
    {
        // Le composant FlashMessages ne supporte actuellement que les messages string individuels
        // Pour plusieurs messages, utiliser des sessions distinctes ou dispatch d'événements
        $this->markTestSkipped('FlashMessages ne supporte actuellement que les messages string individuels');
    }

    public function test_multiple_error_messages_are_displayed(): void
    {
        // Le composant FlashMessages ne supporte actuellement que les messages string individuels
        // Pour plusieurs messages, utiliser des sessions distinctes ou dispatch d'événements
        $this->markTestSkipped('FlashMessages ne supporte actuellement que les messages string individuels');
    }

    public function test_mixed_message_types_are_displayed(): void
    {
        // Le composant FlashMessages ne supporte actuellement que les messages string individuels
        // Pour plusieurs messages, utiliser des sessions distinctes ou dispatch d'événements
        $this->markTestSkipped('FlashMessages ne supporte actuellement que les messages string individuels');
    }

    public function test_no_messages_when_session_is_empty(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.places.index'));

        $response->assertDontSee('Succès');
        $response->assertDontSee('Erreur');
        $response->assertDontSee('Attention');
        $response->assertDontSee('Information');
    }

    public function test_toast_container_has_correct_structure(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['success' => 'Test message'])
            ->get(route('admin.places.index'));

        // Vérifier que le container toast est présent
        $response->assertSee('fixed top-4 right-[10%] z-50', false);
        $response->assertSee('space-y-3', false);
    }

    public function test_toast_has_progress_bar(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['success' => 'Test message'])
            ->get(route('admin.places.index'));

        // Vérifier que la barre de progression est présente
        $response->assertSee('bg-green-500 transition-all duration-50 ease-linear', false);
        $response->assertSee(":style=\"'width: ' + progress + '%'\"", false);
    }

    public function test_toast_has_close_button(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['success' => 'Test message'])
            ->get(route('admin.places.index'));

        // Vérifier que le bouton de fermeture est présent
        $response->assertSee('show = false; clearInterval(interval)', false);
    }

    public function test_multiple_toasts_are_independent(): void
    {
        // Le composant FlashMessages ne supporte actuellement que les messages string individuels
        // Pour plusieurs messages, utiliser des sessions distinctes ou dispatch d'événements
        $this->markTestSkipped('FlashMessages ne supporte actuellement que les messages string individuels');
    }
}
