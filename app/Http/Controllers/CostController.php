<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Cost;
use Illuminate\Http\Response;


class CostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/costs",
     *     summary="Get all costs",
     *     tags={"Costs"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a list of all costs.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="costs",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Costs retrieved successfully."
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
     *                     example="Error occurred while retrieving costs."
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

    public function index()
    {
        try {
            $costs = Cost::all();
            return response()->json([
                'data' => ['costs' => $costs, 'message' => 'Costs retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while retrieving costs.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/costs",
     *     summary="Create a new cost",
     *     tags={"Costs"},
     *     security={{"bearerAuth":{}}},
     *     description="Create a new cost record.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date", "cost_type_id", "desc"},
     *             @OA\Property(property="date", type="string", format="date", example="2024-03-23"),
     *             @OA\Property(property="cost_type_id", type="integer", example="1"),
     *             @OA\Property(property="desc", type="string", example="Description of the cost"),
     *             @OA\Property(property="price", type="integer", example=322)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cost created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost created successfully."
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
     *                     example="Error occurred while storing cost."
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
                'date' => 'required|date',
                'cost_type_id' => 'required|exists:cost_types,id',
                'desc' => 'required|string',
                'price' => 'required|integer'
            ]);

            $cost = Cost::create($request->all());
            return response()->json([
                'data' => ['cost' => $cost, 'message' => 'Cost created successfully.'],
                'status_page' => Response::HTTP_CREATED
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while storing cost.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/costs/{id}",
     *     summary="Get a specific cost",
     *     tags={"Costs"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a specific cost by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the cost to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost retrieved successfully."
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
     *                     example="Error occurred while retrieving cost."
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

    public function show($id)
    {
        try {
            $cost = Cost::findOrFail($id);
            return response()->json([
                'data' => ['cost' => $cost, 'message' => 'Cost retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while retrieving cost.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/costs/{id}",
     *     summary="Update an existing cost",
     *     tags={"Costs"},
     *     security={{"bearerAuth":{}}},
     *     description="Update an existing cost record.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the cost to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date", "cost_type_id", "desc"},
     *             @OA\Property(property="date", type="string", format="date", example="2024-03-23"),
     *             @OA\Property(property="cost_type_id", type="integer", example="1"),
     *             @OA\Property(property="desc", type="string", example="Updated description of the cost"),
     *             @OA\Property(property="price", type="integer", example=190)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="cost",
     *                     type="object"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost updated successfully."
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
     *                     example="Error occurred while updating cost."
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
            $request->validate([
                'date' => 'required|date',
                'cost_type_id' => 'required|exists:cost_types,id',
                'desc' => 'required|string',
                'price' => 'required|integer'

            ]);

            $cost = Cost::findOrFail($id);
            $cost->update($request->all());
            return response()->json([
                'data' => ['cost' => $cost, 'message' => 'Cost updated successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while updating cost.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/costs/{id}",
     *     summary="Delete a specific cost",
     *     tags={"Costs"},
     *     security={{"bearerAuth":{}}},
     *     description="Delete a specific cost by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the cost to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cost deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Cost deleted successfully."
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
     *                     example="Error occurred while deleting cost."
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
            $cost = Cost::findOrFail($id);
            $cost->delete();
            return response()->json([
                'data' => ['message' => 'Cost deleted successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while deleting cost.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
