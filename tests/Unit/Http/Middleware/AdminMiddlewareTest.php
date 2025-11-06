<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private Admin $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new Admin;

        // Créer la route admin.login pour les redirections
        Route::get('/admin/login', function () {
            return response('Login Page');
        })->name('admin.login');
    }

    public function test_middleware_allows_authenticated_admin_user(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $request = Request::create('/admin/dashboard', 'GET');

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return response('Dashboard Content');
        });

        // Assert
        $this->assertEquals('Dashboard Content', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_allows_authenticated_super_admin_user(): void
    {
        // Arrange
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $this->actingAs($superAdmin);

        $request = Request::create('/admin/dashboard', 'GET');

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return response('Dashboard Content');
        });

        // Assert
        $this->assertEquals('Dashboard Content', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_redirects_unauthenticated_user(): void
    {
        // Arrange - Aucun utilisateur authentifié
        $request = Request::create('/admin/dashboard', 'GET');

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return response('Dashboard Content');
        });

        // Assert
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect(route('admin.login')));
    }

    /**
     * Note : Les tests suivants ne sont pas pertinents pour cette application car :
     * - Le champ 'role' est un ENUM('admin', 'super_admin') avec contrainte NOT NULL
     * - Il n'existe pas d'utilisateurs "normaux" dans la table users
     * - Seuls les administrateurs ont accès à l'application
     * - Les tests de rôle NULL ou invalide généreraient des erreurs SQL
     */
    public function test_middleware_preserves_request_method(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $request = Request::create('/admin/places', 'POST');

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return response()->json(['method' => $req->method()]);
        });

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('POST', json_decode($response->getContent())->method);
    }

    public function test_middleware_preserves_request_parameters(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $request = Request::create('/admin/places?page=2&filter=nasa', 'GET');

        // Act
        $response = $this->middleware->handle($request, function ($req) {
            return response()->json([
                'page' => $req->query('page'),
                'filter' => $req->query('filter'),
            ]);
        });

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('2', $data['page']);
        $this->assertEquals('nasa', $data['filter']);
    }
}
