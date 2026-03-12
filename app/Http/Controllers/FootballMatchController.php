<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FootballMatchController extends Controller
{
    public function index(): JsonResponse
    {
        $matches = FootballMatch::with('organizer')->get();

        return response()->json($matches, 200);
    }

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
            'match' => $match,
        ], 201);
    }

    public function show(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->load('organizer', 'registrations', 'comments');

        return response()->json($footballMatch, 200);
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
            'match' => $footballMatch,
        ], 200);
    }

    public function destroy(FootballMatch $footballMatch): JsonResponse
    {
        $footballMatch->delete();

        return response()->json([
            'message' => 'Match deleted successfully',
        ], 200);
    }

    public function count(): JsonResponse
    {
        $count = FootballMatch::where('status', 'open')->count();

        return response()->json([
            'total_available_matches' => $count,
        ], 200);
    }
}
