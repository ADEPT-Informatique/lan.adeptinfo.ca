<?php

namespace App\Rules\Seat\SeatsIo;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

/**
 * Un siège ne possède pas l'état "arrivé" pour un certain LAN, dans l'API seats.io.
 *
 * Class SeatNotArrivedSeatIo
 */
class SeatNotArrivedSeatIo implements Rule
{
    protected $lanId;

    /**
     * SeatNotArrivedSeatIo constructor.
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
        $lan = Lan::find($this->lanId);

        /*
         * Condition de garde
         * Un LAN correspond à l'id de LAN passé
         * L'id du LAN est un entier
         * L'id du siège est une chaîne de caractères
         */
        if (!is_int($this->lanId) || is_null($lan) || !is_string($seatId)) {
            return true; // Une autre validation devrait échouer
        }

        $seatsClient = new SeatsioClient(env('SEAT_SECRET_KEY'));

        try {
            // Demander à l'API de retrouver le siège pour l'événement du LAN, pour l'id du siège
            $status = $seatsClient->events->retrieveObjectStatus($lan->event_key, $seatId);

            // Vérifier que le statut n'est pas à "arrived"
            return $status->status != 'arrived';
        } catch (SeatsioException $exception) {
            // Si aucun siège n'est trouvé, l'API retourne une erreur
            // Une autre validation devrait échouer
            return true;
        }
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.seat_not_arrived_seat_io');
    }
}
