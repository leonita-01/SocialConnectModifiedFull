<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *   schema="Follow",
 *   type="object",
 *   description="Follow relationship model",
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     format="int64",
 *     description="Unique identifier of the follow relationship"
 *   ),
 *   @OA\Property(
 *     property="follower_user_id",
 *     type="integer",
 *     format="int64",
 *     description="The ID of the user who is following"
 *   ),
 *   @OA\Property(
 *     property="followed_user_id",
 *     type="integer",
 *     format="int64",
 *     description="The ID of the user being followed"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     description="Timestamp when the follow relationship was created"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     description="Timestamp when the follow relationship was last updated"
 *   )
 * )
 */



class FollowerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/follow",
     *     summary="Follow a user",
     *     description="Follow another user.",
     *     operationId="followUser",
     *     tags={"Follow"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"followed_user_id"},
     *             @OA\Property(property="followed_user_id", type="integer", example="1", description="The ID of the user to follow")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User followed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User followed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function follow(Request $request)
    {
        $request->validate([
            'followed_user_id' => 'required|exists:users,id'
        ]);

        $follow = new Follower();
        $follow->follower_user_id = Auth::id();
        $follow->followed_user_id = $request->followed_user_id;
        $follow->save();

        return response()->json(['message' => 'User followed successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/unfollow",
     *     summary="Unfollow a user",
     *     description="Unfollow a user that is previously followed.",
     *     operationId="unfollowUser",
     *     tags={"Follow"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"followed_user_id"},
     *             @OA\Property(property="followed_user_id", type="integer", example="1", description="The ID of the user to unfollow")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User unfollowed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User unfollowed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function unfollow(Request $request)
    {
        $request->validate([
            'followed_user_id' => 'required|exists:users,id'
        ]);

        Follower::where('follower_user_id', Auth::id())
            ->where('followed_user_id', $request->followed_user_id)
            ->delete();

        return response()->json(['message' => 'User unfollowed successfully']);
    }

}
