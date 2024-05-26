<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Cost;
use Illuminate\Http\Response;
use App\Models\CostTypeLimit;
use Illuminate\Support\Facades\Mail;
use App\Mail\CostLimitExceededMail;

class CostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/costs",
     *     summary="Get all costs",
     *     tags={"Costs"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve a list of all costs.",
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
     *         name="start_date",
     *         in="query",
     *         description="Filter costs by start date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-01-01"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter costs by end date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-12-31"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cost_type_name",
     *         in="query",
     *         description="Filter costs by related cost type name",
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

    public function index(Request $request)
    {
        try {
            // Get the current page from the request, default to 1 if not provided
            $currentPage = $request->query('page', 1);

            // Define the number of items per page
            $perPage = 10; // You can adjust this number according to your needs

            // Retrieve costs with pagination
            $query = Cost::query()->with('costType');

            // Filter by start date if provided in the query
            if ($request->has('start_date')) {
                $query->whereDate('date', '>=', $request->input('start_date'));
            }

            // Filter by end date if provided in the query
            if ($request->has('end_date')) {
                $query->whereDate('date', '<=', $request->input('end_date'));
            }

            // Filter by related cost type name if provided in the query
            if ($request->has('cost_type_name')) {
                $query->whereHas('costType', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('cost_type_name') . '%');
                });
            }

            $costs = $query->paginate($perPage, ['*'], 'page', $currentPage);

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
            $this->checkAndNotifyCostLimit($cost);

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
            // Retrieve the cost with its related costType
            $cost = Cost::with('costType')->findOrFail($id);

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
            $this->checkAndNotifyCostLimit($cost);

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

    private function checkAndNotifyCostLimit(Cost $cost)
    {
        // Fetch the cost limits for the user and cost type
        $limits = CostTypeLimit::where('user_id', $cost->user_id)
            ->where('cost_type_id', $cost->cost_type_id)
            ->first();
        if (!$limits) {
            return; // No limits set for this cost type and user
        }

        // Check if the cost exceeds the weekly, monthly, quarterly, yearly, or daily limits
        $exceededLimits = []; // Array to store exceeded limits
        $currentDate = now(); // Current date

        // Check weekly limit
        $weeklyCosts = Cost::where('user_id', $cost->user_id)
            ->where('cost_type_id', $cost->cost_type_id)
            ->whereBetween('date', [$currentDate->startOfWeek(), $currentDate->endOfWeek()])
            ->sum('price');

        if ($weeklyCosts > $limits->weekly_limit) {
            $exceededLimits[] = [
                'period' => 'weekly',
                'exceededAmount' => $weeklyCosts - $limits->weekly_limit
            ];
        }

        // Check daily limit
        $dailyCosts = Cost::where('user_id', $cost->user_id)
            ->where('cost_type_id', $cost->cost_type_id)
            ->whereDate('date', $currentDate) // Filter by the current date
            ->sum('price');

        if ($dailyCosts > $limits->daily_limit) {
            $exceededLimits[] = [
                'period' => 'daily',
                'exceededAmount' => $dailyCosts - $limits->daily_limit
            ];
        }

        // Check monthly limit
        $monthlyCosts = Cost::where('user_id', $cost->user_id)
            ->where('cost_type_id', $cost->cost_type_id)
            ->whereYear('date', $currentDate->year)
            ->whereMonth('date', $currentDate->month)
            ->sum('price');

        if ($monthlyCosts > $limits->monthly_limit) {
            $exceededLimits[] = [
                'period' => 'monthly',
                'exceededAmount' => $monthlyCosts - $limits->monthly_limit
            ];
        }

        // Check quarterly limit
        $quarterlyCosts = Cost::where('user_id', $cost->user_id)
            ->where('cost_type_id', $cost->cost_type_id)
            ->whereYear('date', $currentDate->year)
            ->whereRaw('QUARTER(date) = QUARTER(NOW())')
            ->sum('price');

        if ($quarterlyCosts > $limits->quarterly_limit) {
            $exceededLimits[] = [
                'period' => 'quarterly',
                'exceededAmount' => $quarterlyCosts - $limits->quarterly_limit
            ];
        }

        // Check yearly limit
        $yearlyCosts = Cost::where('user_id', $cost->user_id)
            ->where('cost_type_id', $cost->cost_type_id)
            ->whereYear('date', $currentDate->year)
            ->sum('price');

        if ($yearlyCosts > $limits->yearly_limit) {
            $exceededLimits[] = [
                'period' => 'yearly',
                'exceededAmount' => $yearlyCosts - $limits->yearly_limit
            ];
        }

        // Send separate emails for each exceeded limit
        foreach ($exceededLimits as $exceededLimit) {
            Mail::to($cost->user->email)->send(new CostLimitExceededMail(
                $cost,
                $limits,
                $exceededLimit['exceededAmount'],
                $exceededLimit['period']
            ));
        }
    }


}
