<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_screen_is_available(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Auth/Login')
            ->where('loginUrl', route('login.store')));
    }

    public function test_register_screen_is_not_available(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_seeded_admin_can_authenticate_and_has_admin_role(): void
    {
        $this->seed();

        $user = User::query()->where('email', env('ADMIN_EMAIL', 'admin@mesadeayuda.test'))->firstOrFail();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => env('ADMIN_PASSWORD', 'password'),
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->fresh()->hasRole('ADMIN'));
        $this->assertTrue($user->fresh()->can('usuarios'));
    }
}
