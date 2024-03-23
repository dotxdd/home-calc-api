<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
