<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the category"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Update timestamp"
 *     )
 * )
 */
class Category extends Model
{
    use HasFactory;

    protected $table = 'category';
    protected $fillable = ['name'];



}
