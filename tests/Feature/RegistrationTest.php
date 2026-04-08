<?php

use App\Models\FootballMatch;
use App\Models\User;
use App\Models\Registration;
use App\Models\Comment;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'player']);
});

it('user can register for a match', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/players");

    $response->assertStatus(201)->assertJsonPath('message', 'Registered successfully');
});

it('user cannot register for a full match', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create(['max_players' => 1]);

    Registration::factory()->create(['match_id' => $match->id]);

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/players");

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

    $response = $this->postJson("/api/matches/{$match->id}/players");

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

    $response = $this->deleteJson("/api/matches/{$match->id}/players");

    $response->assertStatus(200)->assertJsonPath('message', 'Unregistered successfully');
});

it('user cannot unregister from a match they are not registered for', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->deleteJson("/api/matches/{$match->id}/players");

    $response->assertStatus(400)->assertJsonPath('message', 'You are not registered for this match');
});

it('user can see their own registrations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Registration::factory()->count(2)->create(['user_id' => $user->id]);
    Registration::factory()->count(2)->create(['user_id' => $otherUser->id]);

    Passport::actingAs($user);

    $response = $this->getJson("/api/users/matches");

    $response->assertStatus(200)->assertJsonCount(2);
});

it('authenticated user can see all registrations', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Registration::factory()->count(3)->create(['match_id' => $match->id]);

    Passport::actingAs($user);

    $response = $this->getJson("/api/matches/{$match->id}/players");

    $response->assertStatus(200)->assertJsonCount(3);
});

it('calculates user stats and assigns the correct rank', function () {
    $user = User::factory()->create();
    Passport::actingAs($user);

    FootballMatch::factory()->create(['organizer_id' => $user->id]);
    Registration::factory()->count(2)->create(['user_id' => $user->id]);
    Comment::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->getJson("/api/users/stats");

    $response->assertStatus(200)
        ->assertJson([
            'matches_organized' => 1,
            'matches_joined' => 2,
            'total_comments' => 3,
            'activity_score' => 23,
            'rank' => 'Amateur',
        ]);
});

it('unauthenticated user cannot see user stats', function () {
    $response = $this->getJson("/api/users/stats");

    $response->assertStatus(401);
});
