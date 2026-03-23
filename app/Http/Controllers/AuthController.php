<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;


class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Register a new user',
        tags: ['Auth']
    )]
    #[OA\RequestBody(
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
    )]
    #[OA\Response(response: 201, description: 'User registered successfully')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:9',
            'skill_level' => 'nullable|in:beginner,intermediate,advanced',
            'favourite_position' => 'nullable|in:goalkeeper,defender,midfielder,striker',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'skill_level' => $validated['skill_level'] ?? null,
            'favourite_position' => $validated['favourite_position'] ?? null,
        ]);

        $user->assignRole('player');

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    #[OA\Post(path: '/api/auth/login', summary: 'Login user and get token', tags: ['Auth'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'pepito@test.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Password987'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Login successful',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
                new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGci...'),
                new OA\Property(property: 'user', type: 'object'),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Invalid credentials')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Logout user and revoke token',
        tags: ['Auth'],
        security: [["bearerAuth" => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Logged out successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
