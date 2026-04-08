<?php

use App\Models\FootballMatch;
use App\Models\User;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'player']);
});

it('anyone can see matches', function () {
    FootballMatch::factory()->count(3)->create();

    $response = $this->getJson('/api/matches');

    $response->assertStatus(200)->assertJsonCount(3);
});

it('anyone can see a match', function () {
    $match = FootballMatch::factory()->create();

    $response = $this->getJson("/api/matches/{$match->id}");

    $response->assertStatus(200);
});

it('authenticated user can create a match', function () {
    $user = User::factory()->create();
    $user->assignRole('player');

    Passport::actingAs($user);

    $response = $this->postJson('/api/matches', [
        'description' => 'Partido de prueba',
        'starts_at' => '2026-06-01 12:00:00',
        'duration' => 90,
        'match_type' => '7v7',
        'max_players' => 14,
        'required_level' => 'beginner',
        'price' => 5.00,
        'location_name' => 'Camp Barcelona ',
        'address' => 'Carrer Gran Via 1',
        'city' => 'Barcelona',
    ]);

    $response->assertStatus(201)->assertJsonStructure(['message', 'match']);
});

it('unauthenticated user cannot create a match', function () {
    $response = $this->postJson('/api/matches', [
        'description' => 'Partido de prueba',
        'starts_at' => '2026-06-01 12:00:00',
        'duration' => 90,
        'match_type' => '7v7',
        'max_players' => 14,
        'location_name' => 'Camp Barcelona',
        'address' => 'Carrer Gran Via 1',
        'city' => 'Barcelona',
    ]);

    $response->assertStatus(401);
});

it('organizer can update their match', function () {
    $user = User::factory()->create();
    $user->assignRole('player');

    $match = FootballMatch::factory()->create(['organizer_id' => $user->id]);

    Passport::actingAs($user);

    $response = $this->putJson("/api/matches/{$match->id}", [
        'description' => 'Match updated successfully',
    ]);

    $response->assertStatus(200)->assertJsonPath('match.description', 'Match updated successfully');
});

it('non organizer cannot update a match', function () {
    $user = User::factory()->create();
    $user->assignRole('player');

    $otherUser = User::factory()->create();
    $otherUser->assignRole('player');

    $match = FootballMatch::factory()->create(['organizer_id' => $otherUser->id]);

    Passport::actingAs($user);

    $response = $this->putJson("/api/matches/{$match->id}", [
        'description' => 'Try to update match',
    ]);

    $response->assertStatus(403);
});

it('admin can delete a match', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $match = FootballMatch::factory()->create();

    Passport::actingAs($admin);

    $response = $this->deleteJson("/api/matches/{$match->id}");

    $response->assertStatus(200)->assertJson(['message' => 'Match deleted successfully']);
});

it('player cannot delete a match', function () {
    $user = User::factory()->create();
    $user->assignRole('player');

    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/matches/{$match->id}");

    $response->assertStatus(403);
});
