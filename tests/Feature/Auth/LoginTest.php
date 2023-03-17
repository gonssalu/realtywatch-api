<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $formData = [
        'email' => 'test@example.com',
        'password' => 'password',
        'device_name' => 'Test Device',
    ];

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => $this->formData['email'],
            'password' => bcrypt($this->formData['password']),
        ]);

        $response = $this->postJson('/login', $this->formData);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'user',
            'access_token',
        ]);
    }

    public function test_user_cant_login_with_blocked_account(): void
    {
        User::factory()->create([
            'email' => $this->formData['email'],
            'password' => bcrypt($this->formData['password']),
            'blocked' => true,
        ]);

        $response = $this->postJson('/login', $this->formData);

        $response->assertStatus(403);

        $response->assertJsonStructure(['message']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->delete('/logout');

        $response->assertStatus(200);
    }
}
