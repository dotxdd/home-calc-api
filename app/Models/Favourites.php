<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Favourites",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="The unique identifier for the favourite"
 *     ),
 *     @OA\Property(
 *         property="generate_date",
 *         type="string",
 *         format="date",
 *         description="The date when the favourite was generated"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string",
 *         description="The value of the favourite"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the favourite"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         format="int64",
 *         description="The ID of the user who created the favourite"
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="integer",
 *         format="int64",
 *         description="The ID of the category to which the favourite belongs"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the favourite was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the favourite was last updated"
 *     )
 * )
 */

class Favourites extends Model
{
    use HasFactory;
    protected $table = 'favourites';

    protected $fillable = ['generate_date', 'value', 'name', 'user_id', 'category_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->belongsTo(Category::class);
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
