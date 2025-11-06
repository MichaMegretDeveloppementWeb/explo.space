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
        $messages = [
            'Lieu créé avec succès',
            'Email envoyé au contributeur',
            'Notification publiée',
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['success' => $messages])
            ->get(route('admin.places.index'));

        foreach ($messages as $message) {
            $response->assertSee($message);
        }
    }

    public function test_multiple_error_messages_are_displayed(): void
    {
        $messages = [
            'Erreur lors de la validation',
            'Erreur lors de la sauvegarde',
            'Erreur lors de l\'envoi de l\'email',
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['error' => $messages])
            ->get(route('admin.places.index'));

        foreach ($messages as $message) {
            $response->assertSee($message);
        }
    }

    public function test_mixed_message_types_are_displayed(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession([
                'success' => ['Opération réussie', 'Sauvegarde effectuée'],
                'warning' => ['Attention aux doublons'],
                'info' => ['Pensez à vérifier les données'],
            ])
            ->get(route('admin.places.index'));

        $response->assertSee('Opération réussie');
        $response->assertSee('Sauvegarde effectuée');
        $response->assertSee('Attention aux doublons');
        $response->assertSee('Pensez à vérifier les données');
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
        $response->assertSee('fixed top-4 right-4 z-50', false);
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
        $response->assertSee('@click="show = false; clearInterval(interval)"', false);
    }

    public function test_multiple_toasts_are_independent(): void
    {
        $messages = [
            'Message 1',
            'Message 2',
            'Message 3',
        ];

        $response = $this->actingAs($this->admin)
            ->withSession(['success' => $messages])
            ->get(route('admin.places.index'));

        // Vérifier que tous les messages sont affichés
        foreach ($messages as $message) {
            $response->assertSee($message);
        }

        // Vérifier que chaque toast a sa propre barre de progression
        $content = $response->getContent();
        $progressBars = substr_count($content, 'bg-green-500 transition-all duration-50 ease-linear');

        $this->assertEquals(3, $progressBars, 'Chaque toast doit avoir sa propre barre de progression indépendante');
    }
}
