<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{

    public function index(FootballMatch $footballMatch): JsonResponse
    {
        $comments = $footballMatch->comments()->with('user')->get();

        return response()->json(CommentResource::collection($comments), 200);
    }

    public function store(Request $request, FootballMatch $footballMatch): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'match_id' => $footballMatch->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => new CommentResource($comment)
        ], 201);
    }
}
