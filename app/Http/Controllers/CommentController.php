<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    #[OA\Get(
        path: '/api/matches/{footballMatch}/comments',
        summary: 'Get comments of a match',
        security: [['bearerAuth' => []]],
        tags: ['Comments']
    )]
    #[OA\Parameter(name: 'footballMatch', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)]
    #[OA\Response(response: 200, description: 'List of comments')]
    public function index(FootballMatch $footballMatch): JsonResponse
    {
        $comments = $footballMatch->comments()->with('user')->get();

        return response()->json(CommentResource::collection($comments), 200);
    }

    #[OA\Post(
        path: '/api/matches/{footballMatch}/comments',
        summary: 'Create a comment for a match',
        security: [['bearerAuth' => []]],
        tags: ['Comments']
    )]
    #[OA\Parameter(name: 'footballMatch', in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1)]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['content'],
            properties: [
                new OA\Property(property: 'content', type: 'string', example: 'Apuntado al partido!'),
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Comment created successfully')]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error')]
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
