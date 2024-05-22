<?php

namespace App\Services;

use App\Models\Cost;
use App\Models\CostType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CostStatsService
{
    public static function getDailyCostStats(string $date)
    {
        $user = Auth::user();

        $cost = DB::table('costs')
            ->join('cost_types', 'costs.cost_type_id', '=', 'cost_types.id')
            ->where('costs.date', $date)
          ->where('costs.user_id', $user->id)
            ->selectRaw('SUM(costs.price) as price, costs.cost_type_id, cost_types.name as cost_type_name')
            ->groupBy('costs.cost_type_id', 'cost_types.name')
            ->get();

        return $cost;
    }
    public static function getMonthlyCostStats(string $date)
    {
        $date = Carbon::parse($date);
        $user = Auth::user();

        $costs = DB::table('costs')
            ->join('cost_types', 'costs.cost_type_id', '=', 'cost_types.id')
            ->whereMonth('costs.date', $date->month)
            ->whereYear('costs.date', $date->year)
            ->where('costs.user_id', $user->id)
            ->selectRaw('SUM(costs.price) as price, costs.cost_type_id, cost_types.name as cost_type_name')
            ->groupBy('costs.cost_type_id', 'cost_types.name')
            ->get();

        return $costs;
    }

    public static function getQuarterlyCostStats(string $date)
    {
        $date = Carbon::parse($date);
        $startOfQuarter = $date->copy()->startOfQuarter()->toDateString();
        $endOfQuarter = $date->copy()->endOfQuarter()->toDateString();
        $user = Auth::user();

        $costs = DB::table('costs')
            ->join('cost_types', 'costs.cost_type_id', '=', 'cost_types.id')
            ->whereBetween('costs.date', [$startOfQuarter, $endOfQuarter])
            ->where('costs.user_id', $user->id)
            ->selectRaw('SUM(costs.price) as price, costs.cost_type_id, cost_types.name as cost_type_name')
            ->groupBy('costs.cost_type_id', 'cost_types.name')
            ->get();

        return $costs;
    }

    public static function getYearlyCostStats(string $date)
    {
        $date = Carbon::parse($date);

        $startOfYear = $date->copy()->startOfYear()->toDateString();
        $endOfYear = $date->copy()->endOfYear()->toDateString();
        $user = Auth::user();


        $costs = DB::table('costs')
            ->join('cost_types', 'costs.cost_type_id', '=', 'cost_types.id')
            ->whereBetween('costs.date', [$startOfYear, $endOfYear])
            ->selectRaw('SUM(costs.price) as price, costs.cost_type_id, cost_types.name as cost_type_name')
            ->groupBy('costs.cost_type_id', 'cost_types.name')
            ->where('costs.user_id', $user->id)
            ->get();

        return $costs;
    }


}
