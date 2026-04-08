<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class UserDocs
{
  #[OA\Get(
    path: '/api/users/matches',
    summary: 'Get matches of authenticated user',
    security: [['bearerAuth' => []]],
    tags: ['User'],
    responses: [
      new OA\Response(response: 200, description: 'List of user matches'),
      new OA\Response(response: 401, description: 'Unauthenticated')
    ]
  )]
  public function userMatches() {}

  #[OA\Get(
    path: '/api/users/stats',
    summary: 'Get activity statistics and rank of the authenticated user',
    security: [['bearerAuth' => []]],
    tags: ['User'],
    responses: [
      new OA\Response(
        response: 200,
        description: 'User statistics calculated successfully',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'matches_organized', type: 'integer', example: 1),
            new OA\Property(property: 'matches_joined', type: 'integer', example: 0),
            new OA\Property(property: 'total_comments', type: 'integer', example: 0),
            new OA\Property(property: 'activity_score', type: 'integer', example: 10),
            new OA\Property(property: 'rank', type: 'string', example: 'Rookie', enum: ['Rookie', 'Amateur', 'Pro', 'Legend'])
          ]
        )
      ),
      new OA\Response(response: 401, description: 'Unauthenticated')
    ]
  )]
  public function userStats() {}

  #[OA\Patch(
    path: '/api/users/profile',
    summary: 'Update authenticated user profile',
    security: [['bearerAuth' => []]],
    tags: ['User'],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        properties: [
          new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
          new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
          new OA\Property(property: 'phone', type: 'string', maxLength: 9, nullable: true),
          new OA\Property(property: 'skill_level', type: 'string', enum: ['beginner', 'intermediate', 'advanced']),
          new OA\Property(property: 'favourite_position', type: 'string', enum: ['goalkeeper', 'defender', 'midfielder', 'striker']),
          new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
          new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
        ]
      )
    ),
    responses: [
      new OA\Response(response: 200, description: 'Profile updated successfully'),
      new OA\Response(response: 401, description: 'Unauthenticated'),
      new OA\Response(response: 422, description: 'Validation error')
    ]
  )]
  public function update() {}
}
