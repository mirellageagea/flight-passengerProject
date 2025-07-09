<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        // create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123', 
            'role' => 'admin'
        ]);

        // make login request
        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        // check for successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'access_token',
                'token_type',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }
}
