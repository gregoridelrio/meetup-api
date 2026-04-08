<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class RegistrationDocs
{
  #[OA\Post(
    path: '/api/matches/{footballMatch}/players',
    summary: 'Register for a match',
    security: [['bearerAuth' => []]],
    tags: ['Registrations'],
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
      new OA\Response(response: 201, description: 'Registered successfully'),
      new OA\Response(response: 400, description: 'Match is full or already registered'),
      new OA\Response(response: 401, description: 'Unauthenticated')
    ]
  )]
  public function register() {}

  #[OA\Delete(
    path: '/api/matches/{footballMatch}/players',
    summary: 'Unregister from a match',
    security: [['bearerAuth' => []]],
    tags: ['Registrations'],
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
      new OA\Response(response: 200, description: 'Unregistered successfully'),
      new OA\Response(response: 400, description: 'Not registered for this match'),
      new OA\Response(response: 401, description: 'Unauthenticated')
    ]
  )]
  public function unregister() {}

  #[OA\Get(
    path: '/api/matches/{footballMatch}/players',
    summary: 'Get players registered to a match',
    security: [['bearerAuth' => []]],
    tags: ['Registrations'],
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
      new OA\Response(response: 200, description: 'List of players'),
      new OA\Response(response: 401, description: 'Unauthenticated')
    ]
  )]
  public function players() {}
}
