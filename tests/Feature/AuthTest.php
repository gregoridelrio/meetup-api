<?php

use App\Models\User;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'player']);

    $this->artisan('passport:keys');
    $this->artisan('passport:client', [
        '--personal' => true,
        '--name' => 'Test Personal Access Client',
        '--provider' => 'users',
    ]);
});

it('user can register', function () {

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)->assertJsonStructure(['message', 'token', 'user']);
});

it('user cannot register with invalid data', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'password' => '123',
        'password_confirmation' => '456',
    ]);

    $response->assertStatus(422);
});

it('user can login', function () {

    $user = User::factory()->create([
        'email' => 'test@test.com',
        'password' => Hash::make('password123'),
    ]);
    $user->assignRole('player');

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@test.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'token', 'user']);
});

it('user cannot login with wrong credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@test.com',
        'password' => Hash::make('password123'),
    ]);
    $user->assignRole('player');

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

it('user can logout', function () {
    $user = User::factory()->create();
    $user->assignRole('player');

    Passport::actingAs($user);

    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logged out successfully']);
});
