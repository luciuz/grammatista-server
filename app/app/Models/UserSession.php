<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property bool    $user_id
 * @property string  $token
 * @property array   $body
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Carbon  $expired_at
 */
class UserSession extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_session';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'body',
        'expired_at',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'body' => 'array',
    ];
}
