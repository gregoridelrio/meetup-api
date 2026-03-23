<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class RegistrationController extends Controller
{
    #[OA\Post(
        path: '/api/matches/{footballMatch}/register',
        summary: 'Register for a match',
        security: [['bearerAuth' => []]],
        tags: ['Registrations']
    )]
    #[OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Response(response: 201, description: 'Registered successfully')]
    #[OA\Response(response: 400, description: 'Match is full or already registered')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function register(FootballMatch $footballMatch): JsonResponse
    {
        $user = Auth::user();

        if ($footballMatch->registrations()->count() >= $footballMatch->max_players) {
            return response()->json([
                'message' => 'Match is full',
            ], 400);
        }

        $alreadyRegistered = $footballMatch->registrations()
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyRegistered) {
            return response()->json([
                'message' => 'You are already registered for this match',
            ], 400);
        }

        $registration = Registration::create([
            'user_id' => $user->id,
            'match_id' => $footballMatch->id,
        ]);

        return response()->json([
            'message' => 'Registered successfully',
            'registration' => $registration,
        ], 201);
    }

    #[OA\Delete(
        path: '/api/matches/{footballMatch}/register',
        summary: 'Unregister from a match',
        security: [['bearerAuth' => []]],
        tags: ['Registrations']
    )]
    #[OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Response(response: 200, description: 'Unregistered successfully')]
    #[OA\Response(response: 400, description: 'Not registered for this match')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function unregister(FootballMatch $footballMatch): JsonResponse
    {
        $user = Auth::user();

        $registration = $footballMatch->registrations()
            ->where('user_id', $user->id)
            ->first();

        if (!$registration) {
            return response()->json([
                'message' => 'You are not registered for this match',
            ], 400);
        }

        $registration->delete();

        return response()->json([
            'message' => 'Unregistered successfully',
        ], 200);
    }

    #[OA\Get(
        path: '/api/matches/{footballMatch}/players',
        summary: 'Get players registered to a match',
        security: [['bearerAuth' => []]],
        tags: ['Registrations']
    )]
    #[OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Response(response: 200, description: 'List of players')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function players(FootballMatch $footballMatch): JsonResponse
    {
        $players = $footballMatch->registrations()->with('user')->get();

        return response()->json($players, 200);
    }

    #[OA\Get(
        path: '/api/user/matches',
        summary: 'Get matches of authenticated user',
        security: [['bearerAuth' => []]],
        tags: ['Registrations']
    )]
    #[OA\Response(response: 200, description: 'List of user matches')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function userMatches(): JsonResponse
    {
        $matches = Auth::user()->registrations()->with('match')->get();

        return response()->json($matches, 200);
    }
}
