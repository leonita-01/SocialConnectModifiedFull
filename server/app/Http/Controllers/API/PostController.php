<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 *
 * @OA\Schema(
 *    schema="Post",
 *    type="object",
 *    required={"id", "content"},
 *    properties={
 *      @OA\Property(property="id", type="integer", format="int64", example=1),
 *      @OA\Property(property="content", type="string", example="This is a great post about API documentation."),
 *      @OA\Property(property="image", type="string", example="http://example.com/image.jpg"),
 *      @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2020-01-01T00:00:00Z"),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2020-01-01T12:00:00Z"),
 *      @OA\Property(property="likes_count", type="integer", example=5),
 *      @OA\Property(property="isLiked", type="boolean", example=true)
 *    }
 *  )
 *
 * @OA\Schema(
 *    schema="Pagination",
 *    type="object",
 *    properties={
 *      @OA\Property(property="current_page", type="integer", example=1),
 *      @OA\Property(property="total_pages", type="integer", example=15),
 *      @OA\Property(property="total_items", type="integer", example=150),
 *      @OA\Property(property="has_more_pages", type="boolean", example=true)
 *    }
 *  )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="List all posts with pagination",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved list of posts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="posts", type="array", @OA\Items(ref="#/components/schemas/Post")),
     *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $perPage = 10;

        $posts = Post::query()
                     ->with(['user:id,name'])
                     ->withCount('likedBy as likes_count')
                     ->paginate($perPage)
                     ->appends(request()->query());

        foreach ($posts as $post) {
            $post->isLiked = $post->likedBy()->where('user_id', $userId)->exists();
        }

        return response()->json([
            'posts' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'total_pages' => $posts->lastPage(),
                'total_items' => $posts->total(),
                'has_more_pages' => $posts->hasMorePages(),
            ]
        ]);
    }


    /**
    @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="This is a great post!"),
     *             @OA\Property(property="image", type="string", format="binary", description="Post image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = new Post;
        $post->content = $request->get('content');
        $post->user_id = auth()->id();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts_images', 'public');
            $post->image = $imagePath;
        }

        $post->save();

        return response()->json(['message' => 'Post created successfully', 'post' => $post]);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Retrieve a single post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved post",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function show($id): JsonResponse
    {
        $post = Post::with('user')->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Update an existing post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the post to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="content", type="string", example="Updated post content"),
     *             @OA\Property(property="image", type="string", format="binary", description="Updated post image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     *
     */
    public function update(Request $request, $id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('content')) {
            $post->content = $request->get('content');
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts_images', 'public');
            $post->image = $imagePath;
        }

        $post->save();

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the post to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function destroy($id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

         Storage::delete('public/' . $post->image);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}/like",
     *     summary="Like or unlike a post",
     *     operationId="toggleLike",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post to like or unlike",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post liked successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function toggleLike($id): JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $user = auth()->user();

        if ($post->likedBy->contains($user)) {
            $post->likedBy()->detach($user);
            $message = 'Post unliked successfully';
        } else {
            $post->likedBy()->attach($user);
            $message = 'Post liked successfully';
        }

        return response()->json(['message' => $message]);
    }
}
