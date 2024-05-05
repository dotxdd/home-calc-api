<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Keyword",
 *     title="Keyword",
 *     description="Keyword model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the keyword"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string",
 *         description="Value of the keyword"
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

class Keywords extends Model
{
    use HasFactory;

    protected $table = 'keywords';

    protected $fillable = ['value'];
}
