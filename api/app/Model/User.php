<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;

/**
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string password
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'id', 'created_at', 'updated_at',
    ];

    public function lan()
    {
        return $this->belongsToMany(Lan::class, 'reservation')
            ->using(Reservation::class)
            ->as('reservation')
            ->withPivot('seat_id')
            ->withTimestamps();
    }
}
