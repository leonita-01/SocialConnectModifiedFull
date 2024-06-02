<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *   schema="Group",
 *   type="object",
 *   description="Details about a group",
 *   @OA\Property(property="id", type="integer", description="Unique identifier of the Group"),
 *   @OA\Property(property="name", type="string", description="Name of the Group"),
 *   @OA\Property(property="description", type="string", description="Description of the Group")
 * )
 */

class GroupController extends Controller
{

    /**
     * @OA\Get(
     *   path="/api/groups",
     *   operationId="getGroups",
     *   tags={"Groups"},
     *   summary="Retrieve all groups",
     *   description="Returns a list of all groups stored in the database.",
     *   @OA\Response(
     *     response=200,
     *     description="A list of groups",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Group")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Authentication credentials were not provided.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="An error occurred while processing the request.")
     *     )
     *   )
     * )
     */
    public function index() {
        $groups = Group::all();
        return response()->json($groups);
    }

    /**
 * @OA\Post(
 *     path="/api/groups",
 *     operationId="storeGroup",
 *     tags={"Groups"},
 *     summary="Create a new group",
 *     description="Stores a new group in the database",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Data for the new group",
 *         @OA\JsonContent(
 *             required={"name", "owner_id"},
 *             @OA\Property(property="name", type="string", description="The name of the group"),
 *             @OA\Property(property="description", type="string", description="The description of the group"),
 *             @OA\Property(property="owner_id", type="integer", description="The owner user ID of the group")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Group created",
 *         @OA\JsonContent(ref="#/components/schemas/Group")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Bad request")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation error"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */




    public function show($id)
    {
        $group = Group::findOrFail($id);
        return response()->json($group);
    }

/**
 * @OA\Put(
 *     path="/api/groups/{id}",
 *     operationId="updateGroup",
 *     tags={"Groups"},
 *     summary="Update an existing group",
 *     description="Updates the specified group with new data",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the group to update",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Group data to update",
 *         @OA\JsonContent(
 *             required={"name", "owner_id"},
 *             @OA\Property(property="name", type="string", description="The name of the group", example="New Group Name"),
 *             @OA\Property(property="description", type="string", description="The description of the group", example="New Description"),
 *             @OA\Property(property="owner_id", type="integer", description="The owner user ID of the group", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Group updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Group")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input, object invalid",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid request data")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Group not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Group not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation errors"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */



    /**
     * @OA\Put(
     *   path="/api/groups/{id}",
     *   operationId="updateGroup",
     *   tags={"Groups"},
     *   summary="Update an existing group",
     *   description="Updates group details based on the provided ID and request data.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="The ID of the group to update",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Data for updating the group",
     *     @OA\JsonContent(
     *       required={"name", "owner_id"},
     *       @OA\Property(property="name", type="string", description="Name of the group"),
     *       @OA\Property(property="description", type="string", description="Description of the group", nullable=true),
     *       @OA\Property(property="owner_id", type="integer", description="The user ID of the group owner")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Group updated successfully",
     *     @OA\JsonContent(ref="#/components/schemas/Group")
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Invalid input provided")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Group not found",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Group not found")
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="errors", type="object", example={"name": "Name field is required"})
     *     )
     *   )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id'
        ]);

        $group = Group::findOrFail($id);
        $group->update($request->all());
        return response()->json($group, 200);
    }

    /**
 * @OA\Delete(
 *     path="/api/groups/{id}",
 *     operationId="deleteGroup",
 *     tags={"Groups"},
 *     summary="Delete a specific group",
 *     description="Deletes a group by its unique identifier",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the group to delete",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No content, successful deletion"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Group not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Group not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */
    /**
     * @OA\Delete(
     *   path="/api/groups/{id}",
     *   operationId="deleteGroup",
     *   tags={"Groups"},
     *   summary="Delete a specific group",
     *   description="Deletes a group by its unique identifier. This operation cannot be undone.",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="The ID of the group to delete",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=204,
     *     description="No content, indicates successful deletion with no return data."
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not found, no group found with the specified ID.",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Group not found")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized, if the user is not authenticated or lacks the necessary permissions.",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal server error, generic error message when an unexpected condition was encountered.",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Internal server error")
     *     )
     *   )
     * )
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return response()->json(null, 204);
    }

}
