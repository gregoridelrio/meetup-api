<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class MatchesDocs
{
  #[OA\Get(
    path: "/api/matches",
    summary: "Get list of matches",
    tags: ["Matches"],
    responses: [
      new OA\Response(response: 200, description: "List of matches")
    ]
  )]
  public function index() {}

  #[OA\Post(
    path: '/api/matches',
    summary: 'Create a new match',
    tags: ['Matches'],
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ['starts_at', 'duration', 'match_type', 'max_players', 'location_name', 'address', 'city'],
        properties: [
          new OA\Property(property: 'description', type: 'string', example: 'Partido de futbol casual'),
          new OA\Property(property: 'starts_at', type: 'string', example: '2026-04-01 18:00:00'),
          new OA\Property(property: 'duration', type: 'integer', example: 90),
          new OA\Property(property: 'match_type', type: 'string', example: '7v7'),
          new OA\Property(property: 'max_players', type: 'integer', example: 14),
          new OA\Property(property: 'required_level', type: 'string', enum: ['beginner', 'intermediate', 'advanced']),
          new OA\Property(property: 'price', type: 'number', example: 5.00),
          new OA\Property(property: 'location_name', type: 'string', example: 'Camp Municipal'),
          new OA\Property(property: 'address', type: 'string', example: 'Carrer Gran Via 1'),
          new OA\Property(property: 'city', type: 'string', example: 'Barcelona'),
        ]
      )
    ),
    responses: [
      new OA\Response(response: 201, description: 'Match created successfully'),
      new OA\Response(response: 401, description: 'Unauthenticated'),
      new OA\Response(response: 422, description: 'Validation error')
    ]
  )]
  public function store() {}

  #[OA\Get(
    path: '/api/matches/{footballMatch}',
    summary: 'Get a match by ID',
    tags: ['Matches'],
    parameters: [
      new OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
      )
    ],
    responses: [
      new OA\Response(response: 200, description: "Match details"),
      new OA\Response(response: 404, description: 'Match not found')
    ]
  )]
  public function show() {}

  #[OA\Put(
    path: '/api/matches/{footballMatch}',
    summary: 'Update a match',
    security: [['bearerAuth' => []]],
    tags: ['Matches'],
    parameters: [
      new OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
      )
    ],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        properties: [
          new OA\Property(property: 'description', type: 'string', example: 'Partido modificado'),
          new OA\Property(property: 'starts_at', type: 'string', example: '2026-04-01 18:00:00'),
          new OA\Property(property: 'duration', type: 'integer', example: 90),
          new OA\Property(property: 'match_type', type: 'string', example: '7v7'),
          new OA\Property(property: 'max_players', type: 'integer', example: 14),
          new OA\Property(property: 'required_level', type: 'string', enum: ['beginner', 'intermediate', 'advanced']),
          new OA\Property(property: 'price', type: 'number', example: 5.00),
          new OA\Property(property: 'location_name', type: 'string', example: 'Camp Municipal'),
          new OA\Property(property: 'address', type: 'string', example: 'Carrer Gran Via 1'),
          new OA\Property(property: 'city', type: 'string', example: 'Barcelona'),
          new OA\Property(property: 'status', type: 'string', enum: ['open', 'full', 'cancelled']),
        ]
      )
    ),
    responses: [
      new OA\Response(response: 200, description: 'Match updated successfully'),
      new OA\Response(response: 401, description: 'Unauthenticated'),
      new OA\Response(response: 403, description: 'Unauthorized'),
      new OA\Response(response: 404, description: 'Match not found')
    ]
  )]
  public function update() {}

  #[OA\Delete(
    path: '/api/matches/{footballMatch}',
    summary: 'Delete a match',
    security: [['bearerAuth' => []]],
    tags: ['Matches'],
    parameters: [
      new OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
      )
    ],
    responses: [
      new OA\Response(response: 200, description: 'Match deleted successfully'),
      new OA\Response(response: 401, description: 'Unauthenticated'),
      new OA\Response(response: 403, description: 'Unauthorized'),
      new OA\Response(response: 404, description: 'Match not found')
    ]
  )]
  public function destroy() {}
}
