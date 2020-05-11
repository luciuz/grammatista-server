<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property bool    $is_complete
 * @property integer $lesson_id
 * @property integer $test_id
 * @property integer $user_id
 * @property array   $question
 * @property array   $answer
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Carbon  $expired_at
 * @property Carbon  $finished_at
 * @property Carbon  $deleted_at
 */
class Variant extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'variant';

    /**
     * @var array
     */
    protected $fillable = [
        'is_complete',
        'lesson_id',
        'test_id',
        'user_id',
        'question',
        'answer',
        'expired_at',
        'finished_at',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
        'finished_at',
        'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'question' => 'array',
        'answer'   => 'array',
    ];
}
