<?php

use App\Models\FootballMatch;
use App\Models\User;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'player']);
});

it('user can register for a match', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/register");

    $response->assertStatus(201)->assertJsonPath('message', 'Registered successfully');
});

it('user cannot register for a full match', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create(['max_players' => 1]);

    Registration::factory()->create(['match_id' => $match->id]);

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/register");

    $response->assertStatus(400)->assertJsonPath('message', 'Match is full');
});

it('user cannot register twice for the same match', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Registration::factory()->create([
        'user_id' => $user->id,
        'match_id' => $match->id
    ]);

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/register");

    $response->assertStatus(400)->assertJsonPath('message', 'You are already registered for this match');
});

it('user can unregister from a match', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Registration::factory()->create([
        'user_id' => $user->id,
        'match_id' => $match->id
    ]);

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/matches/{$match->id}/register");

    $response->assertStatus(200)->assertJsonPath('message', 'Unregistered successfully');
});

it('user cannot unregister from a match they are not registered for', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/matches/{$match->id}/register");

    $response->assertStatus(400)->assertJsonPath('message', 'You are not registered for this match');
});

it('user can see their own registrations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Registration::factory()->count(2)->create(['user_id' => $user->id]);
    Registration::factory()->count(2)->create(['user_id' => $otherUser->id]);

    Passport::actingAs($user);

    $response = $this->getJson("/api/user/matches");

    $response->assertStatus(200)->assertJsonCount(2);
});
