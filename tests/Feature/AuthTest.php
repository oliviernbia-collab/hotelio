<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Login ────────────────────────────────────────────────────────────────

    public function test_login_page_is_accessible(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'client',
        ]);

        $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ])->assertRedirect();

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email']);
    }

    public function test_login_is_rate_limited(): void
    {
        User::factory()->create(['email' => 'rate@example.com', 'password' => Hash::make('pw')]);

        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['email' => 'rate@example.com', 'password' => 'wrong']);
        }

        $this->post('/login', ['email' => 'rate@example.com', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    // ── Register ─────────────────────────────────────────────────────────────

    public function test_register_page_is_accessible(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'role'                  => 'client',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->post('/register', [
            'name'                  => 'Test',
            'email'                 => 'existing@example.com',
            'role'                  => 'client',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertSessionHasErrors(['email']);
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'client']);
        $this->actingAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    // ── Guest redirect ───────────────────────────────────────────────────────

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
