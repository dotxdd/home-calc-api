<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Phpml\Regression\LeastSquares;

class RegressionService
{
    public function getHistoricalCostData()
    {
        $user = Auth::user();

        $costs = DB::table('costs')
            ->join('cost_types', 'costs.cost_type_id', '=', 'cost_types.id')
            ->where('costs.user_id', $user->id)
            ->whereDate('costs.date', '>=', now()->subDays(31))
            ->select('costs.date', 'costs.price', 'costs.cost_type_id', 'cost_types.name as cost_type_name')
            ->get();

        return $costs;
    }


    public function prepareData($costs)
    {
        $data = [];
        $targets = [];

        foreach ($costs as $cost) {
            $timestamp = strtotime($cost->date);
            if (!isset($targets[$cost->cost_type_id])) {
                $targets[$cost->cost_type_id] = ['prices' => [], 'data' => []];
            }
            $targets[$cost->cost_type_id]['data'][] = [$timestamp];
            $targets[$cost->cost_type_id]['prices'][] = $cost->price;
        }

        return [$data, $targets];
    }

    public function trainModels($data, $targets)
    {
        $models = [];

        foreach ($targets as $costTypeId => $target) {
            $regression = new LeastSquares();
            $regression->train($target['data'], $target['prices']);
            $models[$costTypeId] = $regression;
        }

        return $models;
    }

    public function predictFutureSpending($models)
    {
        $predictions = [];
        $startDate = strtotime('today');
        $endDate = strtotime('+31 days');

        foreach ($models as $costTypeId => $model) {
            $predictions[$costTypeId] = [];
            for ($date = $startDate; $date <= $endDate; $date += 86400) { // Increment by one day
                $predictedPrice = $model->predict([$date]);
                $predictions[$costTypeId][] = [
                    'date' => date('Y-m-d', $date),
                    'predicted_price' => $predictedPrice // Directly assign the predicted price
                ];
            }
        }

        return $predictions;
    }
}
