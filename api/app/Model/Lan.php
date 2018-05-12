<?php

namespace App\Model;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property DateTime lan_start
 * @property DateTime lan_end
 * @property DateTime seat_reservation_start
 * @property DateTime tournament_reservation_start
 * @property string event_key_id
 * @property string public_key_id
 * @property string secret_key_id
 * @property int price
 */
class Lan extends Model
{
    protected $table = 'lan';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'reservation')
            ->using(Reservation::class)
            ->as('reservation')
            ->withPivot('seat_id')
            ->withTimestamps();
    }
}
