<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $lesson_id
 * @property string  $locale
 * @property array   $question
 * @property array   $answer
 * @property array   $duration Duration in seconds.
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Carbon  $deleted_at
 */
class Test extends Model
{
    /**
     * @var string
     */
    protected $table = 'test';

    /**
     * @var array
     */
    protected $fillable = [
        'lesson_id',
        'locale',
        'question',
        'answer',
        'duration',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
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
