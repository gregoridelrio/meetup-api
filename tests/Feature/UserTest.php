<?php

use App\Models\User;
use App\Models\FootballMatch;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'player']);
});

it('can list the matches the user is registered in', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    Registration::factory()->create([
        'user_id' => $user->id,
        'match_id' => $match->id
    ]);

    $response = $this->getJson('/api/users/matches');


    $response->assertStatus(200)
        ->assertJsonCount(1);
});

it('returns correct user statistics', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->getJson('/api/users/stats');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'matches_organized',
            'matches_joined',
            'total_comments',
            'activity_score',
            'rank'
        ]);
});

it('can update user profile information', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    $newData = [
        'name' => 'Lewandowski',
        'skill_level' => 'advanced',
        'favourite_position' => 'midfielder'
    ];

    $response = $this->patchJson('/api/users', $newData);

    $response->assertStatus(200)
        ->assertJsonPath('user.name', 'Lewandowski');
});

it('cannot update email to one that already exists', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['email' => 'otheruser@test.com']);

    Passport::actingAs($user);

    $response = $this->patchJson('/api/users', [
        'email' => 'otheruser@test.com'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('updates the password correctly', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->patchJson('/api/users', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!'
    ]);

    $response->assertStatus(200);
});

it('cannot update password without confirmation', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->patchJson('/api/users', [
        'password' => 'NewPassword123!'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('cannot update password with mismatched confirmation', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    $response = $this->patchJson('/api/users', [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'DifferentPassword123!'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
