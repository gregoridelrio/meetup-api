<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use App\Http\Resources\FootballMatchResource;

class FootballMatchController extends Controller
{
    #[OA\Get(
        path: "/api/matches",
        summary: "Get list of matches",
        tags: ["Matches"],
    )]
    #[OA\Response(response: 200, description: "List of matches")]
    public function index(): JsonResponse
    {
        $matches = FootballMatch::with('organizer')->get();

        return response()->json(FootballMatchResource::collection($matches), 200);
    }

    #[OA\Post(
        path: '/api/matches',
        summary: 'Create a new match',
        tags: ['Matches'],
        security: [["bearerAuth" => []]],
    )]
    #[OA\RequestBody(
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
    )]
    #[OA\Response(response: 201, description: 'Match created successfully')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'duration' => 'required|integer|min:1',
            'match_type' => 'required|string|max:45',
            'max_players' => 'required|integer|min:1',
            'required_level' => 'nullable|in:beginner,intermediate,advanced',
            'price' => 'nullable|numeric',
            'location_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
        ]);

        $match = FootballMatch::create([
            ...$validated,
            'organizer_id' => Auth::id(),
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Match created successfully',
            'match' => new FootballMatchResource($match),
        ], 201);
    }

    #[OA\Get(
        path: '/api/matches/{footballMatch}',
        summary: 'Get a match by ID',
        tags: ['Matches']
    )]
    #[OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Response(response: 200, description: "Match details")]
    #[OA\Response(response: 404, description: 'Match not found')]
    public function show(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->load('organizer', 'registrations', 'comments');

        return response()->json(new FootballMatchResource($footballMatch), 200);
    }

    #[OA\Put(
        path: '/api/matches/{footballMatch}',
        summary: 'Update a match',
        security: [['bearerAuth' => []]],
        tags: ['Matches']
    )]
    #[OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\RequestBody(
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
    )]
    #[OA\Response(response: 200, description: 'Match updated successfully')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 403, description: 'Unauthorized')]
    #[OA\Response(response: 404, description: 'Match not found')]
    public function update(Request $request, FootballMatch $footballMatch): JsonResponse
    {
        if (Auth::id() !== $footballMatch->organizer_id && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'description' => 'nullable|string|max:255',
            'starts_at' => 'nullable|date',
            'duration' => 'nullable|integer',
            'match_type' => 'nullable|string|max:45',
            'max_players' => 'nullable|integer',
            'required_level' => 'nullable|in:beginner,intermediate,advanced',
            'price' => 'nullable|numeric',
            'location_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'status' => 'nullable|in:open,full,cancelled',
        ]);

        $footballMatch->update($validated);

        return response()->json([
            'message' => 'Match updated successfully',
            'match' => new FootballMatchResource($footballMatch),
        ], 200);
    }

    #[OA\Delete(
        path: '/api/matches/{footballMatch}',
        summary: 'Delete a match',
        security: [['bearerAuth' => []]],
        tags: ['Matches']
    )]
    #[OA\Parameter(
        name: 'footballMatch',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Response(response: 200, description: 'Match deleted successfully')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 403, description: 'Unauthorized')]
    #[OA\Response(response: 404, description: 'Match not found')]
    public function destroy(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->delete();

        return response()->json([
            'message' => 'Match deleted successfully',
        ], 200);
    }
}
