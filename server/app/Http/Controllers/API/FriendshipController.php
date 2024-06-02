<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *   schema="Friendship",
 *   type="object",
 *   properties={
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="friend_id", type="integer", example=2),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
 *   }
 * )
 */
class FriendshipController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/friends",
     *     summary="List all friends with pagination",
     *     tags={"Friendships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved list of friends with pagination",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="friends", type="array", @OA\Items(ref="#/components/schemas/Friendship")),
     *             @OA\Property(property="pagination", ref="#/components/schemas/Pagination")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $perPage = 10;

        $friends = Friendship::with(['user', 'friend'])
            ->where('user_id', $user->id)
            ->orWhere('friend_id', $user->id)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->paginate($perPage)
            ->appends($request->query());

        $friends->getCollection()->transform(function ($friendship) use ($user) {
            if ($friendship->user_id === $user->id) {
                $friendship->friend = $friendship->friend->only(['id', 'name']);
            } else {
                $friendship->friend = $friendship->user->only(['id', 'name']);
            }

            unset($friendship->user);

            return $friendship;
        });

        return response()->json([
            'friends' => $friends->items(),
            'pagination' => [
                'current_page' => $friends->currentPage(),
                'total_pages' => $friends->lastPage(),
                'total_items' => $friends->total(),
                'has_more_pages' => $friends->hasMorePages(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/friends/pending",
     *     summary="List all pending friendship requests",
     *     tags={"Friendships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved list of pending friendship requests",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pendingRequests", type="array", @OA\Items(ref="#/components/schemas/Friendship")),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="total_pages", type="integer"),
     *                 @OA\Property(property="total_items", type="integer"),
     *                 @OA\Property(property="has_more_pages", type="boolean")
     *             )
     *         )
     *     )
     * )
     */
    public function pending(Request $request): JsonResponse
    {
        $user = auth()->user();
        $perPage = 10;

        $pendingRequests = Friendship::with('user') // Eager load the user relationship
        ->where('friend_id', $user->id)
            ->where('status', Friendship::STATUS_PENDING)
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'pendingRequests' => $pendingRequests->items(),
            'pagination' => [
                'current_page' => $pendingRequests->currentPage(),
                'total_pages' => $pendingRequests->lastPage(),
                'total_items' => $pendingRequests->total(),
                'has_more_pages' => $pendingRequests->hasMorePages(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/friends",
     *     summary="Create a new friendship request",
     *     tags={"Friendships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"friend_id"},
     *             @OA\Property(property="friend_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Friendship request created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Friendship")
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="A friendship request already exists or is pending between these users",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="A friendship request already exists or is pending between these users.")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $friendId = $request->input('friend_id');

        $existingFriendship = Friendship::where(function ($query) use ($user, $friendId) {
            $query->where('user_id', $user->id)
                ->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($user, $friendId) {
            $query->where('user_id', $friendId)
                ->where('friend_id', $user->id);
        })->whereIn('status', [Friendship::STATUS_PENDING, Friendship::STATUS_ACCEPTED])
            ->first();

        if ($existingFriendship) {
            return response()->json([
                'message' => 'A friendship request already exists or is pending between these users.'
            ], 409);
        }

        $friendship = Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => Friendship::STATUS_PENDING,
        ]);

        return response()->json($friendship, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/friends/{friendship}",
     *     summary="Update the status of a friendship",
     *     tags={"Friendships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="friendship",
     *         in="path",
     *         required=true,
     *         description="The ID of the friendship to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="accepted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Friendship status updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Friendship")
     *     )
     * )
     */
    public function update(Request $request, Friendship $friendship): JsonResponse
    {
        $status = $request->status;

        if (!in_array($status, [Friendship::STATUS_ACCEPTED, Friendship::STATUS_REJECTED])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $friendship->update(['status' => $status]);

        return response()->json($friendship);
    }

    /**
     * @OA\Delete(
     *     path="/api/friends/{friendship}",
     *     summary="Delete a friendship",
     *     tags={"Friendships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="friendship",
     *         in="path",
     *         required=true,
     *         description="The ID of the friendship to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Friendship deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Friendship deleted")
     *         )
     *     )
     * )
     */
    public function destroy(Friendship $friendship): JsonResponse
    {
        $friendship->delete();
        return response()->json(['message' => 'Friendship deleted']);
    }
}
