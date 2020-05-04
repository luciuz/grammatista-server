<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string  $transaction_token
 * @property array   $result
 * @property Carbon  $created_at
 */
class TransactionToken extends Model
{
    /**
     * @var string
     */
    protected $table = 'transaction_token';

    /**
     * @var array
     */
    protected $fillable = [
        'transaction_token',
        'result',
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

    /**
     * @var string[]
     */
    protected $casts = [
        'result' => 'array',
    ];
}
