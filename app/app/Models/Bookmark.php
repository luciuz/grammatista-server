<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $lesson_id
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Carbon  $deleted_at
 */
class Bookmark extends Model
{
    /**
     * @var string
     */
    protected $table = 'bookmark';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
