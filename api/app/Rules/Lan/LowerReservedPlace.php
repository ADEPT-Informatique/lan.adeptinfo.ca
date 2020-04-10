<?php

namespace App\Rules\Lan;

use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

/**
 * Le nombre de réservations dans un LAN est moins grand que le nombre spécifié.
 *
 * Class LowerReservedPlace
 */
class LowerReservedPlace implements Rule
{
    protected $lanId;

    /**
     * LowerReservedPlace constructor.
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
     * @param int    $places
     *
     * @return bool
     */
    public function passes($attribute, $places): bool
    {
        /*
         * Conditions de garde :
         * L'id du LAN est un entier
         * Le nombre de places attendues est un entier
         */
        if (!is_int($this->lanId) || !is_int($places)) {
            return true;
        }

        $placeCount = Reservation::where('lan_id', $this->lanId)->count();

        return $placeCount <= $places;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.lower_reserved_place');
    }
}
