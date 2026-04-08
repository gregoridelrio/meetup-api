<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\RegistrationResource;

class UserController extends Controller
{

    public function userMatches(): JsonResponse
    {
        $matches = Auth::user()->registrations()->with('match')->get();

        return response()->json(RegistrationResource::collection($matches), 200);
    }

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
