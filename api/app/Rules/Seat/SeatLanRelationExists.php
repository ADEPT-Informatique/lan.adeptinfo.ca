<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

/**
 * Une réservation entre un siège et un LAN.
 *
 * Class SeatLanRelationExists
 */
class SeatLanRelationExists implements Rule
{
    protected $lanId;
    protected $seatId;

    /**
     * SeatLanRelationExists constructor.
     *
     * @param string|null $lanId Id du LAN
     */
    public function __construct($lanId)
    {
        $this->lanId = $lanId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param string $seatId
     *
     * @return bool
     */
    public function passes($attribute, $seatId): bool
    {
        // Mettre l'id du siège dans une variable globale pour
        $this->seatId = $seatId;

        /*
         * Condition de garde :
         * Un LAN correspond à l'id de LAN passé
         * L'id du LAN est un entier
         * L'id du siège est une chaîne de caractères
        */
        if (is_null(Lan::find($this->lanId)) || !is_int($this->lanId) || !is_string($seatId)) {
            return true; // Une autre validation devrait échouer
        }

        // Rechercher s'il existe une réservation ayant l'id du LAN et l'id du siège
        return Reservation::where('lan_id', $this->lanId)
                ->where('seat_id', $seatId)->first() != null;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.seat_lan_relation_exists', ['seat_id' => $this->seatId, 'lan_id' => $this->lanId]);
    }
}
