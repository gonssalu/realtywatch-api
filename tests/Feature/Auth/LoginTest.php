<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login(): void
    {
        $user = User::factory()->create();

        $hasUser = $user ? true : false;

        $this->assertTrue($hasUser);

        $response = $this->actingAs($user)->get('/api/user');

        $response->assertStatus(200);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->delete('/logout');

        $response->assertStatus(200);
    }
}
