<?php

namespace App\Services;

use App\Models\Cost;
use Carbon\Carbon;
use Illuminate\Http\Response;

class DatesService
{
    public static function getCarbonDateFromRequest(string $date)
    {
        // Check if the date is not given or if it doesn't match the expected format
        if (!$date || !Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json([
                'data' => ['errors' =>'Invalid date format. Please use Y-m-d format.', 'message' => 'Error occurred while passing data.'],
                'status_page' => Response::HTTP_UNPROCESSABLE_ENTITY
            ]);
        }

        // Create a Carbon instance from the given date
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);

        return  $carbonDate->format('Y-m-d');
    }

}
