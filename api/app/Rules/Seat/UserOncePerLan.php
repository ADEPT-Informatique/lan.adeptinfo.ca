<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use App\Model\Reservation;
use App\Model\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un utilisateur ne possède une réservation qu'une fois dans un LAN.
 *
 * Class UserOncePerLan
 */
class UserOncePerLan implements Rule
{
    protected $user;
    protected $email;

    /**
     * UserOncePerLan constructor.
     *
     * @param Authenticatable|null $user  Utilisateur
     * @param string|null          $email Courriel de l'utilisateur
     */
    public function __construct(?Authenticatable $user, $email)
    {
        $this->user = $user;
        $this->email = $email;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $lanId     Id du LAN
     *
     * @return bool
     */
    public function passes($attribute, $lanId): bool
    {
        /*
         * Conditions de garde :
         * L'id du LAN est un entier
         * L'id du LAN correspond à un LAN
         * Si aucun utilisateur n'est fourni, l'adresse courriel est une chaîne de caractères,
         * et correspond à un utilisateur
         */
        if (
            !is_int($lanId) ||
            is_null(Lan::find($lanId)) ||
            (
                is_null($this->user) &&
                (
                    !is_string($this->email) ||
                    is_null($this->user = User::where('email', $this->email)->first())
                )
            )
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher une réservation ayant l'id de l'utilisateur et l'id du LAN
        $lanUserReservation = Reservation::where('user_id', $this->user->id)
            ->where('lan_id', $lanId)->first();

        // Si des réservation a été trouvée et que le nombre de réservation est plus grand que 0
        if (!is_null($lanUserReservation) && $lanUserReservation->count() > 0) {

            // La validation échoue
            return false;
        }

        return true;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.user_once_per_lan');
    }
}
