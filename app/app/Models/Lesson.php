<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $user_id Author.
 * @property string  $locale
 * @property string  $title
 * @property array   $body
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Carbon  $published_at
 * @property Carbon  $deleted_at
 */
class Lesson extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'lesson';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'locale',
        'title',
        'body',
        'published_at',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'published_at',
        'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'body' => 'array',
    ];
}
