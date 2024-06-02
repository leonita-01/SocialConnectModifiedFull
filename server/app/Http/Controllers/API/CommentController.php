<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="Comment",
 *   type="object",
 *   properties={
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="post_id", type="integer", example=1),
 *     @OA\Property(property="content", type="string", example="This is a comment"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
 *   }
 * )
 */
class CommentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Create a new comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"post_id", "content"},
     *             @OA\Property(property="post_id", type="integer", example=1),
     *             @OA\Property(property="content", type="string", example="This is a comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'post_id' => 'required|integer',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $validatedData['post_id'],
            'content' => $validatedData['content'],
        ]);

        return response()->json($comment, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{comment}",
     *     summary="Update a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         description="The ID of the comment to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Updated comment content")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     )
     * )
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $validatedData['content'],
        ]);

        return response()->json($comment);
    }

    /**
     * @OA\Get(
     *     path="/api/comments/{comment}",
     *     summary="Get a single comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         description="The ID of the comment to fetch",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the comment",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     )
     * )
     */
    public function show(Comment $comment): JsonResponse
    {
        return response()->json($comment);
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{comment}",
     *     summary="Delete a comment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         description="The ID of the comment to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Comment deleted successfully"
     *     )
     * )
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(null, 204);
    }
}
