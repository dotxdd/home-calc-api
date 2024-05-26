<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\RegressionService;
use Exception;

class PredictionController extends Controller
{
    protected $regressionService;

    public function __construct(RegressionService $regressionService)
    {
        $this->regressionService = $regressionService;
    }

    /**
     * @OA\Get(
     *     path="/api/predictions",
     *     summary="Get spending predictions for the next 30 days",
     *     tags={"Predictions"},
     *     security={{"bearerAuth":{}}},
     *     description="Predict how much a user will spend on each cost type over the next 30 days using linear regression.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="predictions",
     *                     type="array",
     *                     @OA\Items(type="object")
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="Predictions retrieved successfully."
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
     *                     example="Error occurred while retrieving predictions."
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
    public function getPredictions()
    {
        try {
            // Step 1: Gather Historical Data
            $costs = $this->regressionService->getHistoricalCostData();
            // Step 2: Prepare the Data
            list($data, $targets) = $this->regressionService->prepareData($costs);

            // Step 3: Train the Models
            $models = $this->regressionService->trainModels($data, $targets);

            // Step 4: Make Predictions
            $predictions = $this->regressionService->predictFutureSpending($models);

            return response()->json([
                'data' => ['predictions' => $predictions, 'message' => 'Predictions retrieved successfully.'],
                'status_page' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            return response()->json([
                'data' => ['errors' => $e->getMessage(), 'message' => 'Error occurred while retrieving predictions.'],
                'status_page' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
