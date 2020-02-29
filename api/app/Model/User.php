<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Seatsio\SeatsioClient;

/**
 * Utilisateur enregistré dans l'application par courriel, Facebook, ou Google.
 *
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string password
 * @property int id
 * @property string facebook_id
 * @property mixed confirmation_code
 * @property bool is_confirmed
 * @property string google_id
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens;
    use Authenticatable;
    use Authorizable;
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * Champs qui ne sont pas retournés par défaut lorsque l'objet est retourné dans une requête HTTP.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'id', 'created_at', 'updated_at', 'facebook_id', 'google_id', 'confirmation_code', 'is_confirmed',
    ];

    /**
     * Obtenir le nom complet d'un utilisateur (Prénom et nom).
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function contribution()
    {
        return $this->hasMany(Contribution::class);
    }

    public function lan()
    {
        return $this->hasManyThrough(
            'App\Model\Lan',
            'App\Model\Reservation'
        );
    }

    protected static function boot()
    {
        parent::boot();

        // Avant la suppression de l'utilisateur
        static::deleting(function ($user) {
            $reservations = Reservation::where('user_id', $user->id)->get();
            // Pour chaque réservation d'un utilisateur
            foreach ($reservations as $reservation) {
                $lan = Lan::find($reservation->lan_id);

                // Rendre sa place disponible dans l'API Seats.io
                $seatsClient = new SeatsioClient(env('SEAT_SECRET_KEY'));
                $seatsClient->events->release($lan->event_key, $reservation->seat_id);

                // Supprimer la réservation
                $reservation->delete();
            }

            DB::table('contribution')
                ->where('user_id', $user->id)
                ->delete();

            // TODO Supprimer les liens avec les rôles de LAN
            // TODO Supprimer les liens avec les rôles globaux
            // TODO Supprimer les liens avec les organisations de tournoi
            // TODO Supprimer les liens avec les tags
        });
    }
}
