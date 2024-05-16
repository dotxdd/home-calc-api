<?php

namespace App\Http\Controllers;

use App\Models\CostTypeLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;


class CostTypeLimitController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cost-types-limits",
     *     summary="Get all cost type limits",
     *     tags={"Cost Type Limits"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a list of all cost type limits.",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination (default: 1)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cost_type_name",
     *         in="query",
     *         description="Filter cost type limits by related cost type name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type_limits",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limits retrieved successfully."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="string",
     *                     example="Error message"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Error occurred while retrieving cost type limits."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        try {
            // Get the current page from the request, default to 1 if not provided
            $currentPage = $request->query('page', 1);

            // Define the number of items per page
            $perPage = 10; // You can adjust this number according to your needs

            // Retrieve cost type limits with pagination
            $query = CostTypeLimit::query()->with('costType');

            // Filter by related cost type name if provided in the query
            if ($request->has('cost_type_name')) {
                $query->whereHas('costType', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('cost_type_name') . '%');
                });
            }

            $costTypeLimits = $query->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json([
                'data' => ['cost_type_limits' => $costTypeLimits, 'message' => 'Cost type limits retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while retrieving cost type limits.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/cost-types-limits",
     *     summary="Create a new cost type limit",
     *     tags={"Cost Type Limits"},
     *     security={{"bearerAuth":{}}},
     *     description="Create a new cost type limit.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Cost type limit data",
     *         @OA\JsonContent(
     *             required={"cost_type_id", "user_id"},
     *             @OA\Property(property="cost_type_id", type="integer", example="1"),
     *             @OA\Property(property="weekly_limit", type="number", example="100.50"),
     *             @OA\Property(property="monthly_limit", type="number", example="500.00"),
     *             @OA\Property(property="quarter_limit", type="number", example="1500.00"),
     *             @OA\Property(property="yearly_limit", type="number", example="6000.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cost type limit created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type_limit",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limit created successfully."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=201
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=422
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="string",
     *                     example="Error message"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Error occurred while creating cost type limit."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cost_type_id' => 'required|exists:cost_types,id',
            'weekly_limit' => 'numeric|nullable',
            'monthly_limit' => 'numeric|nullable',
            'quarter_limit' => 'numeric|nullable',
            'yearly_limit' => 'numeric|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $userId = Auth::id();
            $costTypeLimit = CostTypeLimit::updateOrCreate(
                [
                    'cost_type_id' => $request->input('cost_type_id'),
                    'user_id' => $userId
                ],
                $request->only(['weekly_limit', 'monthly_limit', 'quarter_limit', 'yearly_limit'])
            );

            return response()->json([
                'data' => [
                    'cost_type_limit' => $costTypeLimit,
                    'message' => $costTypeLimit->wasRecentlyCreated ? 'Cost type limit created successfully.' : 'Cost type limit updated successfully.'
                ],
                'status_page' => $costTypeLimit->wasRecentlyCreated ? Response::HTTP_CREATED : Response::HTTP_OK
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while creating/updating cost type limit.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/cost-types-limits/{id}",
     *     summary="Get a specific cost type limit",
     *     tags={"Cost Type Limits"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a specific cost type limit by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the cost type limit to retrieve",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type_limit",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limit retrieved successfully."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cost type limit not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="string",
     *                     example="Error message"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limit not found."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=404
     *             )
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $costTypeLimit = CostTypeLimit::where('id',$id)->with('costType')->first();
            return response()->json([
                'data' => ['cost_type_limit' => $costTypeLimit, 'message' => 'Cost type limit retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Cost type limit not found.'],
                'status_page' => Response::HTTP_NOT_FOUND
            ]);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/cost-types-limits/{id}",
     *     summary="Update a specific cost type limit",
     *     tags={"Cost Type Limits"},
     *     security={{"bearerAuth":{}}},
     *     description="Update a specific cost type limit by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the cost type limit to update",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Cost type limit data",
     *         @OA\JsonContent(
     *             required={"cost_type_id", "user_id"},
     *             @OA\Property(property="cost_type_id", type="integer", example="1"),
     *             @OA\Property(property="user_id", type="integer", example="1"),
     *             @OA\Property(property="weekly_limit", type="number", example="100.50"),
     *             @OA\Property(property="monthly_limit", type="number", example="500.00"),
     *             @OA\Property(property="quarter_limit", type="number", example="1500.00"),
     *             @OA\Property(property="yearly_limit", type="number", example="6000.00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost type limit updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type_limit",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limit updated successfully."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=422
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="string",
     *                     example="Error message"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Error occurred while updating cost type limit."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $costTypeLimit = CostTypeLimit::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'cost_type_id' => 'exists:cost_types,id',
                'user_id' => 'exists:users,id',
                'weekly_limit' => 'numeric|nullable',
                'monthly_limit' => 'numeric|nullable',
                'quarter_limit' => 'numeric|nullable',
                'yearly_limit' => 'numeric|nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $costTypeLimit->update($request->all());

            return response()->json([
                'data' => ['cost_type_limit' => $costTypeLimit, 'message' => 'Cost type limit updated successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while updating cost type limit.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/cost-types-limits/{id}",
     *     summary="Delete a specific cost type limit",
     *     tags={"Cost Type Limits"},
     *     security={{"bearerAuth":{}}},
     *     description="Delete a specific cost type limit by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the cost type limit to delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost type limit deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limit deleted successfully."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cost type limit not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="string",
     *                     example="Error message"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type limit not found."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=404
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="string",
     *                     example="Error message"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Error occurred while deleting cost type limit."
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status_page",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $costTypeLimit = CostTypeLimit::findOrFail($id);
            $costTypeLimit->delete();

            return response()->json([
                'data' => ['message' => 'Cost type limit deleted successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while deleting cost type limit.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
