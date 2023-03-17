<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private $formData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'Test Device',
    ];

    public function test_new_user_can_register(): void
    {
        $response = $this->post('/register', $this->formData);

        $response->assertStatus(200);
    }
}
