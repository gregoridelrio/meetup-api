<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\FootballMatchResource;

class FootballMatchController extends Controller
{
    public function index(): JsonResponse
    {
        $matches = FootballMatch::with('organizer')->get();

        return response()->json(FootballMatchResource::collection($matches), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'duration' => 'required|integer|min:1',
            'match_type' => 'required|string|max:45|in:5v5,7v7,11v11',
            'max_players' => 'required|integer|min:2|max:50',
            'required_level' => 'nullable|in:beginner,intermediate,advanced',
            'price' => 'nullable|numeric|min:0',
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

    public function show(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->load('organizer', 'registrations', 'comments');

        return response()->json(new FootballMatchResource($footballMatch), 200);
    }

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

    public function destroy(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->delete();

        return response()->json([
            'message' => 'Match deleted successfully',
        ], 200);
    }
}
