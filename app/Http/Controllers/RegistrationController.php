<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RegistrationResource;

class RegistrationController extends Controller
{
    public function register(FootballMatch $footballMatch): JsonResponse
    {
        $user = Auth::user();

        if ($footballMatch->status !== 'open') {
            return response()->json([
                'message' => 'Match is not open for registration',
            ], 400);
        }

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
            'registration' => new RegistrationResource($registration),
        ], 201);
    }

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

    public function players(FootballMatch $footballMatch): JsonResponse
    {
        $players = $footballMatch->registrations()->with('user')->get();

        return response()->json(RegistrationResource::collection($players), 200);
    }
}
