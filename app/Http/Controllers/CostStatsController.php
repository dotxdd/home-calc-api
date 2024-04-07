<?php

namespace App\Http\Controllers;

use App\Models\CostTypeLimit;
use App\Services\CostStatsService;
use App\Services\DatesService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;


class CostStatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/daily/costs/stats",
     *     summary="Get daily cost statistics",
     *     tags={"Cost Stats"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve statistics for daily costs for a given date.",
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="The date for which to retrieve cost statistics, in 'YYYY-MM-DD' format.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-04-07"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object"),
     *                 example={{"cost_type_name":"utility","cost_type_id":2,"price":100},{"cost_type_name":"grocery","cost_type_id":1,"price":200}}
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Daily cost statistics retrieved successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid date format."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An error occurred while processing your request."
     *             )
     *         )
     *     )
     * )
     */

    public function getDailyCosts(Request $request)
    {

        try {
            $dateInput = $request->input('date');
            $date = DatesService::getCarbonDateFromRequest($dateInput);
            $costs = CostStatsService::getDailyCostStats($date);

            return response()->json(['data' => ['message' => 'success', 'data' => $costs], 'status_page' => 200]);
        } catch (\Exception $e) {

            return response()->json(['data' => ['message' => 'Failed to retrieve cost data', 'errors' => $e->getMessage()], 'status_page' => 401] );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/monthly/costs/stats",
     *     summary="Get monthly cost statistics",
     *     tags={"Cost Stats"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve statistics for monthly costs for a given date.",
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="The date for which to retrieve cost statistics, in 'YYYY-MM-DD' format.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-04-07"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object"),
     *                 example={{"cost_type_name":"utility","cost_type_id":2,"price":100},{"cost_type_name":"grocery","cost_type_id":1,"price":200}}
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Daily cost statistics retrieved successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid date format."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An error occurred while processing your request."
     *             )
     *         )
     *     )
     * )
     */

    public function getMonthlyCosts(Request $request)
    {

        try {
            $dateInput = $request->input('date');
            $date = DatesService::getCarbonDateFromRequest($dateInput);
            $costs = CostStatsService::getMonthlyCostStats($date);

            return response()->json(['data' => ['message' => 'success', 'data' => $costs], 'status_page' => 200]);
        } catch (\Exception $e) {

            return response()->json(['data' => ['message' => 'Failed to retrieve cost data', 'errors' => $e->getMessage()], 'status_page' => 401] );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/quarterly/costs/stats",
     *     summary="Get quarterly cost statistics",
     *     tags={"Cost Stats"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve statistics for quarterly costs for a given date.",
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="The date for which to retrieve cost statistics, in 'YYYY-MM-DD' format.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-04-07"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object"),
     *                 example={{"cost_type_name":"utility","cost_type_id":2,"price":100},{"cost_type_name":"grocery","cost_type_id":1,"price":200}}
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Daily cost statistics retrieved successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid date format."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An error occurred while processing your request."
     *             )
     *         )
     *     )
     * )
     */

    public function getQuarterlyCosts(Request $request)
    {

        try {
            $dateInput = $request->input('date');
            $date = DatesService::getCarbonDateFromRequest($dateInput);
            $costs = CostStatsService::getQuarterlyCostStats($date);

            return response()->json(['data' => ['message' => 'success', 'data' => $costs], 'status_page' => 200]);
        } catch (\Exception $e) {

            return response()->json(['data' => ['message' => 'Failed to retrieve cost data', 'errors' => $e->getMessage()], 'status_page' => 401] );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/yearly/costs/stats",
     *     summary="Get yearly cost statistics",
     *     tags={"Cost Stats"},
     *     security={{"bearerAuth":{}}},
     *     description="Retrieve statistics for yearly costs for a given date.",
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="The date for which to retrieve cost statistics, in 'YYYY-MM-DD' format.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-04-07"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object"),
     *                 example={{"cost_type_name":"utility","cost_type_id":2,"price":100},{"cost_type_name":"grocery","cost_type_id":1,"price":200}}
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Daily cost statistics retrieved successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid date format."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="An error occurred while processing your request."
     *             )
     *         )
     *     )
     * )
     */
    public function getYearlyCosts(Request $request)
    {

        try {
            $dateInput = $request->input('date');
            $date = DatesService::getCarbonDateFromRequest($dateInput);
            $costs = CostStatsService::getYearlyCostStats($date);

            return response()->json(['data' => ['message' => 'success', 'data' => $costs], 'status_page' => 200]);
        } catch (\Exception $e) {

            return response()->json(['data' => ['message' => 'Failed to retrieve cost data', 'errors' => $e->getMessage()], 'status_page' => 401] );
        }
    }
}
