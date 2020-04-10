<?php

namespace App\Rules\Role;

use App\Model\LanRole;
use Illuminate\Contracts\Validation\Rule;

/**
 * Le nom d'un rôle de LAN n'existe qu'une fois par LAN.
 *
 * Class LanRoleNameOncePerLan
 */
class LanRoleNameOncePerLan implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     *
     * @param null $lanId Id du LAN
     */
    public function __construct($lanId)
    {
        $this->lanId = $lanId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param string $roleName  Nom du rôle
     *
     * @return bool
     */
    public function passes($attribute, $roleName): bool
    {
        /*
         * Condition de garde :
         * L'id du LAN n'est pas nul
         * L'id du LAN est un entier
         * Le nom du rôle est une chaîne de caractères
         */
        if (!is_int($this->lanId) || !is_string($roleName) || is_null($this->lanId)) {
            return true; // Une autre validation devrait échouer
        }

        $lanSeatReservation = LanRole::where('lan_id', $this->lanId)
            ->where('name', $roleName)->first();

        return is_null($lanSeatReservation) || $lanSeatReservation->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.lan_role_name_once_per_lan');
    }
}
