<?php

namespace App\Models;

use App\Notifications\CostLimitExceeded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Cost extends Model
{
    protected $fillable = ['date', 'cost_type_id', 'user_id', 'desc', 'price'];

    public function costType()
    {
        return $this->belongsTo(CostType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model){
            $model->user_id = auth()->id();
        });

        self::addGlobalScope(function(Builder $builder){
            $builder->where('user_id', auth()->id());
        });
    }


    public function checkCostLimit()
    {
        $user = Auth::user();
        $costType = $this->costType;

        $limits = CostTypeLimit::where('user_id', $user->id)
            ->where('cost_type_id', $costType->id)
            ->first();

        if ($limits) {
            $periods = ['weekly', 'monthly', 'quarterly', 'yearly'];
            $limitsArray = [
                'weekly_limit' => 7,
                'monthly_limit' => 30,
                'quarter_limit' => 90,
                'yearly_limit' => 365
            ];

            foreach ($periods as $period) {
                $periodLimit = $limits->{$period . '_limit'};
                if ($periodLimit > 0) {
                    $startDate = now()->subDays($limitsArray[$period . '_limit']);
                    $totalCosts = self::where('cost_type_id', $this->cost_type_id)
                        ->where('user_id', $user->id)
                        ->where('date', '>=', $startDate)
                        ->sum('price');

                    if (($totalCosts + $this->price) > $periodLimit) {
                        $user->notify(new CostLimitExceeded($this, $periodLimit, ucfirst($period)));
                    }
                }
            }
        }
    }
}
