<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
/**
 * @OA\Schema(
 *   schema="Photo",
 *   type="object",
 *   required={"title", "url"},
 *   properties={
 *     @OA\Property(property="id", type="integer", format="int64", description="Unique identifier of the photo"),
 *     @OA\Property(property="title", type="string", description="Title of the photo"),
 *     @OA\Property(property="description", type="string", description="Description of the photo"),
 *     @OA\Property(property="url", type="string", description="URL of the photo image"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date and time when the photo was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date and time when the photo was last updated")
 *   }
 * )
 */
class PhotoController extends Controller
{
    /**
     * Display the specified photo.
     *
     * @OA\Get(
     *      path="/photos/{id}",
     *      tags={"Photos"},
     *      summary="Get a specific photo",
     *      description="Returns the details of a specific photo",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the photo",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Details of the photo",
     *          @OA\JsonContent(ref="#/components/schemas/Photo")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Photo not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        $photo = Photo::find($id);
        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }
        return response()->json($photo, 200);
    }

    /**
     * Update the specified photo in storage.
     *
     * @OA\Put(
     *      path="/photos/{id}",
     *      tags={"Photos"},
     *      summary="Update a photo",
     *      description="Update the details of a photo",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the photo",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="description", type="string", description="Description of the photo")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Photo updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="photo", ref="#/components/schemas/Photo")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Photo not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $photo = Photo::find($id);
        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $request->validate([
            'description' => 'nullable|string',
        ]);

        $photo->description = $request->input('description');
        $photo->save();

        return response()->json(['message' => 'Photo updated successfully', 'photo' => $photo], 200);
    }

    /**
     * Remove the specified photo from storage.
     *
     * @OA\Delete(
     *      path="/photos/{id}",
     *      tags={"Photos"},
     *      summary="Delete a photo",
     *      description="Delete a specific photo",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the photo",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Photo deleted successfully"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Photo not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        $photo = Photo::find($id);
        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $photo->delete();

        return response()->json(null, 204);
    }
}
