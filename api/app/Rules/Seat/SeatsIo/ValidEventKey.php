<?php

namespace App\Rules\Seat\SeatsIo;

use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

/**
 * Une clé d'événement seats.io est valide.
 *
 * Class ValidEventKey
 */
class ValidEventKey implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param string $eventKey
     *
     * @return bool
     */
    public function passes($attribute, $eventKey): bool
    {
        /*
         * Conditions de garde :
         * La longueur de la clé d'événement est plus petite que 255 caractères
        * L'id du LAN est un entier
        * La clé d'événement est une chaîne de caractères
        * La clé secrète est une chaîne de caractères
         */
        if (
            strlen($eventKey) > 255 ||
            !is_string($eventKey)
        ) {
            return true; // Une autre validation devrait échouer
        }

        $seatsClient = new SeatsioClient(env('SEAT_SECRET_KEY'));

        try {
            // Tenter de retrouver l'événement associé à la clé
            $seatsClient->events->retrieve($eventKey);
        } catch (SeatsioException $exception) {
            // Si aucun événement n'a été trouvé, une erreur est lancée
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
        return trans('validation.valid_event_key');
    }
}
