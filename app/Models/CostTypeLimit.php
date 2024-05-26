<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostTypeLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost_type_id',
        'user_id',
        'weekly_limit',
        'monthly_limit',
        'quarter_limit',
        'yearly_limit',
    ];

    public function costType()
    {
        return $this->belongsTo(CostType::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->user_id = auth()->id();
        });

        self::addGlobalScope(function (Builder $builder) {
            $builder->where('user_id', auth()->id());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
