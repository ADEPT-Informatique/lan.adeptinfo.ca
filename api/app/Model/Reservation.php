<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Reservation extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seat_id', 'lan_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'created_at', 'updated_at',
    ];

    protected $casts = ['lan_id' => 'integer'];
}
