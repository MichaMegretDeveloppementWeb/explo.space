<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\AdminInvitation;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_invitation_with_valid_token_redirects_to_password_tab(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'admin',
        ]);

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        Event::fake([Verified::class]);

        // Act
        $response = $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $response->assertRedirect(route('admin.settings.show', ['tab' => 'password']))
            ->assertSessionHas('info', 'Bienvenue ! Veuillez changer votre mot de passe pour sécuriser votre compte.');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_accept_invitation_marks_email_as_verified(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        Event::fake([Verified::class]);

        // Act
        $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_accept_invitation_marks_invitation_as_accepted(): void
    {
        // Arrange
        $user = User::factory()->create();

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        // Act
        $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $invitation->refresh();
        $this->assertNotNull($invitation->accepted_at);
    }

    public function test_accept_invitation_logs_user_in_automatically(): void
    {
        // Arrange
        $user = User::factory()->create();

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        // Act
        $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $this->assertAuthenticatedAs($user);
    }

    public function test_accept_invitation_with_invalid_token_redirects_to_login(): void
    {
        // Act
        $response = $this->get(route('admin.invitation.accept', ['token' => 'invalid-token']));

        // Assert
        $response->assertRedirect(route('admin.login'))
            ->assertSessionHas('error', 'Le lien d\'invitation est invalide ou a expiré. Veuillez contacter un administrateur.');

        $this->assertGuest();
    }

    public function test_accept_invitation_with_expired_token_redirects_to_login(): void
    {
        // Arrange
        $user = User::factory()->create();

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->subDay(), // Expiré
        ]);

        // Act
        $response = $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $response->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_accept_invitation_with_already_accepted_token_redirects_to_login(): void
    {
        // Arrange
        $user = User::factory()->create();

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => now()->subDay(), // Déjà acceptée
            'expires_at' => now()->addDays(7),
        ]);

        // Act
        $response = $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $response->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_accept_invitation_does_not_verify_already_verified_email(): void
    {
        // Arrange
        $verifiedAt = now()->subDay();
        $user = User::factory()->create([
            'email_verified_at' => $verifiedAt,
        ]);

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        Event::fake([Verified::class]);

        // Act
        $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $user->refresh();
        $this->assertEquals($verifiedAt->timestamp, $user->email_verified_at->timestamp);
        Event::assertNotDispatched(Verified::class);
    }

    public function test_url_contains_password_tab_parameter(): void
    {
        // Arrange
        $user = User::factory()->create();

        $invitation = AdminInvitation::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);

        // Act
        $response = $this->get(route('admin.invitation.accept', ['token' => $invitation->token]));

        // Assert
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('tab=password', $redirectUrl);
    }
}
