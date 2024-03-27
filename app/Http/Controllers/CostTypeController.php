<?php
namespace App\Http\Controllers;

use App\Models\CostType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class CostTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cost-types",
     *     summary="Get all cost types",
     *     tags={"Cost Types"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a list of all cost types. You can optionally filter by name.",
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
     *         name="name",
     *         in="query",
     *         description="Filter cost types by name",
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
     *                     property="cost_types",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost types retrieved successfully."
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(
     *                         property="total",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="per_page",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="current_page",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="last_page",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="from",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="to",
     *                         type="integer",
     *                         example=10
     *                     )
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
     *                     example="Error occurred while retrieving cost types."
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

            // Retrieve cost types with pagination
            $query = CostType::query();

            // Filter by name if provided in the query
            if ($request->has('name')) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            }

            $costTypes = $query->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json([
                'data' => ['cost_types' => $costTypes, 'message' => 'Cost types retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while retrieving cost types.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/cost-types",
     *     summary="Create a new cost type",
     *     tags={"Cost Types"},
     *     security={{"bearerAuth":{}}},
     *     description="Create a new cost type.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "desc"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Cost Type Name"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     example="Description of cost type"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cost type created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type created successfully."
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
     *         description="Validation error occurred",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Validation error occurred."
     *                 )
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
     *                     example="Error occurred while creating cost type."
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
        try {
            $request->validate([
                'name' => 'required|string',
                'desc' => 'required|string',
            ]);

            $costType = CostType::create($request->all());
            return response()->json([
                'data' => ['cost_type' => $costType, 'message' => 'Cost type created successfully.'],
                'status_page' => Response::HTTP_CREATED
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => [ 'errors' => $e->getMessage(), 'message' => 'Validation error occurred.'],
                'status_page' => Response::HTTP_UNPROCESSABLE_ENTITY
            ]);

        } catch (Exception $e) {
            return response()->json([
                'data' => [ 'errors' => $e->getMessage(), 'message' => 'Error occurred while creating cost type.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/cost-types/{costType}",
     *     summary="Get a specific cost type",
     *     tags={"Cost Types"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a specific cost type by ID.",
     *     @OA\Parameter(
     *         name="costType",
     *         in="path",
     *         required=true,
     *         description="ID of the cost type to retrieve",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost type retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type retrieved successfully."
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
     *         description="Cost type not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Cost type not found."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string"
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
     *                     example="Error occurred while retrieving cost type."
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
    public function show(CostType $costType)
    {
        try {
            return response()->json([
                'data' => ['cost_type' => $costType, 'message' => 'Cost type retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cost type not found.',
                'errors' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([
                'data' => [ 'errors' => $e->getMessage(), 'message' => 'Error occurred while creating cost type.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/cost-types/{costType}",
     *     summary="Update an existing cost type",
     *     tags={"Cost Types"},
     *     security={{"bearerAuth":{}}},
     *     description="Update an existing cost type by ID.",
     *     @OA\Parameter(
     *         name="costType",
     *         in="path",
     *         required=true,
     *         description="ID of the cost type to update",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "desc"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Updated Cost Type Name"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     example="Updated description of cost type"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost type updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost_type",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost type updated successfully."
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
     *         description="Validation error occurred",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="errors",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Validation error occurred."
     *                 )
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
     *                     example="Error occurred while updating cost type."
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

    public function update(Request $request, CostType $costType)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'desc' => 'required|string',
            ]);

            $costType->update($request->all());
            return response()->json([
                'data' => ['cost_type' => $costType, 'message' => 'Cost type updated successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error occurred.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json([
                'data' => [ 'errors' => $e->getMessage(), 'message' => 'Error occurred while creating cost type.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/cost-types/{costType}",
     *     summary="Delete an existing cost type",
     *     tags={"Cost Types"},
     *     security={{"bearerAuth":{}}},
     *     description="Delete an existing cost type by ID.",
     *     @OA\Parameter(
     *         name="costType",
     *         in="path",
     *         required=true,
     *         description="ID of the cost type to delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cost type deleted successfully"
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
     *                     example="Error occurred while deleting cost type."
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

    public function destroy(CostType $costType)
    {
        try {
            $costType->delete();
            return response()->json([
                'data' => ['cost_type' => null, 'message' => 'Cost type deleted successfully.'],
                'status_page' => Response::HTTP_NO_CONTENT
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => [ 'errors' => $e->getMessage(), 'message' => 'Error occurred while creating cost type.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
