<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\RegistrationResource;
use OpenApi\Attributes as OA;

class UserController extends Controller
{

    #[OA\Get(
        path: '/api/users/matches',
        summary: 'Get matches of authenticated user',
        security: [['bearerAuth' => []]],
        tags: ['User']
    )]
    #[OA\Response(response: 200, description: 'List of user matches')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function userMatches(): JsonResponse
    {
        $matches = Auth::user()->registrations()->with('match')->get();

        return response()->json(RegistrationResource::collection($matches), 200);
    }

    #[OA\Get(
        path: '/api/users/stats',
        summary: 'Get activity statistics and rank of the authenticated user',
        security: [['bearerAuth' => []]],
        tags: ['User']
    )]
    #[OA\Response(
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
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function userStats(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'matches_organized' => $user->organisedMatches()->count(),
            'matches_joined' => $user->registrations()->count(),
            'total_comments' => $user->comments()->count(),
            'activity_score' => $user->getActivityScore(),
            'rank' => $user->getRank(),
        ], 200);
    }

    #[OA\Get(
        path: '/api/users/profile',
        summary: 'Get authenticated user profile',
        security: [['bearerAuth' => []]],
        tags: ['User']
    )]
    #[OA\Response(response: 200, description: 'Profile updated successfully')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:45',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:9',
            'skill_level' => 'sometimes|nullable|in:beginner,intermediate,advanced',
            'favourite_position' => 'sometimes|nullable|in:goalkeeper,defender,midfielder,striker',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user)
        ], 200);
    }
}
