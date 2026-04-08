<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class CommentDocs
{
  #[OA\Get(
    path: '/api/matches/{footballMatch}/comments',
    summary: 'Get comments of a match',
    tags: ['Comments'],
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
      new OA\Response(response: 200, description: 'List of comments'),
      new OA\Response(response: 404, description: 'Match not found')
    ]
  )]
  public function index() {}

  #[OA\Post(
    path: '/api/matches/{footballMatch}/comments',
    summary: 'Create a comment for a match',
    security: [['bearerAuth' => []]],
    tags: ['Comments'],
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
        required: ['content'],
        properties: [
          new OA\Property(property: 'content', type: 'string', example: '¡Apuntado al partido!'),
        ]
      )
    ),
    responses: [
      new OA\Response(response: 201, description: 'Comment created successfully'),
      new OA\Response(response: 401, description: 'Unauthenticated'),
      new OA\Response(response: 422, description: 'Validation error'),
      new OA\Response(response: 404, description: 'Match not found')
    ]
  )]
  public function store() {}
}
