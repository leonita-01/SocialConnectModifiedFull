<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="Story",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the story."
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The unique identifier of the user who posted the story."
 *     ),
 *     @OA\Property(
 *         property="media_path",
 *         type="string",
 *         description="The path to the story's media."
 *     ),
 *     @OA\Property(
 *         property="expiration_time",
 *         type="string",
 *         format="date-time",
 *         description="The expiration time of the story."
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the story was created."
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the story was last updated."
 *     )
 * )
 */

class StoryController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/stories/upload",
     *     summary="Upload a new story",
     *     tags={"Stories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user's story media",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"media"},
     *                 @OA\Property(
     *                     property="media",
     *                     type="string",
     *                     format="binary",
     *                     description="The file of the story media"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Story uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story uploaded successfully"),
     *             @OA\Property(property="path", type="string", example="public/stories/yourstory.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function upload(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4|max:10240', // 10MB Max
        ]);

        $user = Auth::user();
        $mediaFile = $request->file('media');
        $expirationTime = Carbon::now()->addHours(24);


        $mediaPath = $mediaFile->store('public/stories');


        $story = new Story([
            'user_id' => $user->id,
            'media_path' => $mediaPath,
            'expiration_time' => $expirationTime,
        ]);
        $story->save();

        return response()->json(['message' => 'Story uploaded successfully', 'path' => $mediaPath], 201);
    }


    /**
     * @OA\Get(
     *     path="/api/stories/active",
     *     summary="View active stories",
     *     tags={"Stories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Story")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function viewActiveStories()
    {
        $currentTime = Carbon::now();
        $stories = Story::where('expiration_time', '>', $currentTime)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($stories);
    }

    /**
     * @OA\Delete(
     *     path="/api/stories/{id}",
     *     summary="Delete a specific story",
     *     tags={"Stories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the story to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Story deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Story not found or access denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story not found or access denied")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function delete($id)
    {
        $user = Auth::user();
        $story = Story::where('id', $id)->where('user_id', $user->id)->first();

        if (!$story) {
            return response()->json(['message' => 'Story not found or access denied'], 404);
        }

        // Optionally, delete the media file from storage
        Storage::delete($story->media_path);

        $story->delete();

        return response()->json(['message' => 'Story deleted successfully'], 200);
    }


}
