<?php

namespace App\Rules\Seat;

use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un siège n'est pas déjà réservé ou occupé par un utilisateur pour un certain LAN.
 *
 * Class SeatOncePerLan
 */
class SeatOncePerLan implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     *
     * @param string $lanId Id du LAN
     */
    public function __construct($lanId)
    {
        $this->lanId = $lanId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $seatId    Id du siège
     *
     * @return bool
     */
    public function passes($attribute, $seatId): bool
    {
        /*
         * Conditions de garde :
        * L'id du LAN est un entier
        * L'id du siège est une chaîne de caractères
         */
        if (!is_int($this->lanId) || !is_string($seatId)) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher les réservations ayant l'id du LAN et l'id du siège
        $lanSeatReservation = Reservation::where('lan_id', $this->lanId)
            ->where('seat_id', $seatId)->first();

        // Si aucune réservation n'a été trouvée,
        // le nombre de réservation trouvées est à 0, la validation échoue
        return is_null($lanSeatReservation) || $lanSeatReservation->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.seat_once_per_lan');
    }
}
