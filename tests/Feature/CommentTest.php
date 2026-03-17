<?php

use App\Models\Comment;
use App\Models\FootballMatch;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('anyone can see comments for a match', function () {
    $match = FootballMatch::factory()->create();

    Comment::factory()->count(3)->create(['match_id' => $match->id]);
    Comment::factory()->count(3)->create();

    $response = $this->getJson("/api/matches/{$match->id}/comments");

    $response->assertStatus(200)->assertJsonCount(3);
});

it('authenticated user can post a comment', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/comments", [
        'content' => 'Apuntado'
    ]);

    $response->assertStatus(201)->assertJsonPath('comment.content', 'Apuntado');

    $this->assertDatabaseHas('comments', [
        'content' => 'Apuntado',
        'user_id' => $user->id,
        'match_id' => $match->id
    ]);
});

it('unauthenticated user cannot post a comment', function () {
    $match = FootballMatch::factory()->create();

    $response = $this->postJson("/api/matches/{$match->id}/comments", [
        'content' => 'Apuntado'
    ]);

    $response->assertStatus(401);
});

it('cannot post a comment with empty content', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $response = $this->postJson("/api/matches/{$match->id}/comments", [
        'content' => ''
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['content']);
});

it('cannot post a comment longer than 255 characters', function () {
    $user = User::factory()->create();
    $match = FootballMatch::factory()->create();

    Passport::actingAs($user);

    $longContent = str_repeat('a', 256);

    $response = $this->postJson("/api/matches/{$match->id}/comments", [
        'content' => $longContent
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['content']);
});
