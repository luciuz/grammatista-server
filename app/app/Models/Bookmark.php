<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $lesson_id
 * @property Carbon  $created_at
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
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritDoc
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }
}
