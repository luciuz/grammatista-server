<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property bool    $is_active
 * @property integer $vk_id
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 */
class User extends Model
{
    /**
     * @var string
     */
    protected $table = 'user';

    /**
     * @var array
     */
    protected $fillable = [
        'is_active',
        'vk_id',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
