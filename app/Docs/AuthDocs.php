<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class AuthDocs
{
  #[OA\Post(
    path: '/api/auth/register',
    summary: 'Register a new user',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ['name', 'email', 'password', 'password_confirmation'],
        properties: [
          new OA\Property(property: 'name', type: 'string', example: 'Pepito'),
          new OA\Property(property: 'email', type: 'string', example: 'pepito@test.com'),
          new OA\Property(property: 'password', type: 'string', example: 'Password987'),
          new OA\Property(property: 'password_confirmation', type: 'string', example: 'Password987'),
          new OA\Property(property: 'phone', type: 'string', example: '666666666'),
          new OA\Property(property: 'skill_level', type: 'string', enum: ['beginner', 'intermediate', 'advanced']),
          new OA\Property(property: 'favourite_position', type: 'string', enum: ['goalkeeper', 'defender', 'midfielder', 'striker']),
        ]
      )
    ),
    responses: [
      new OA\Response(response: 201, description: 'User registered successfully'),
      new OA\Response(response: 422, description: 'Validation error')
    ]
  )]
  public function register() {}

  #[OA\Post(
    path: '/api/auth/login',
    summary: 'Login user and get token',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ['email', 'password'],
        properties: [
          new OA\Property(property: 'email', type: 'string', format: 'email', example: 'pepito@test.com'),
          new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Password987'),
        ]
      )
    ),
    responses: [
      new OA\Response(
        response: 200,
        description: 'Login successful',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
            new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGci...'),
            new OA\Property(property: 'user', type: 'object'),
          ]
        )
      ),
      new OA\Response(response: 401, description: 'Invalid credentials'),
      new OA\Response(response: 422, description: 'Validation error')
    ]
  )]
  public function login() {}

  #[OA\Post(
    path: '/api/auth/logout',
    summary: 'Logout user and revoke token',
    tags: ['Auth'],
    security: [["bearerAuth" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Logged out successfully',
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
          ]
        )
      ),
      new OA\Response(response: 401, description: 'Unauthenticated')
    ]
  )]
  public function logout() {}
}
