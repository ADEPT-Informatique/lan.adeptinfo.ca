<?php

namespace App\Rules\Role;

use App\Model\GlobalRoleUser;
use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un rôle global n'est associé qu'une fois par utilisateur.
 *
 * Class GlobalRoleOncePerUser
 */
class GlobalRoleOncePerUser implements Rule
{
    protected $email;

    /**
     * SeatOncePerLan constructor.
     *
     * @param null $email Courriel de l'utilisateur
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param  mixed int Id du rôle global
     *
     * @return bool
     */
    public function passes($attribute, $globalRoleId): bool
    {
        /*
         * Conditions de garde :
         * L'id du rôle global n'est pas nul
         * L'id du rôle global est un entier
         * Le courriel de l'utilisateur est une chaîne de caractères
         * Un utilisateur existe pour le courriel
         */
        $user = User::where('email', $this->email)->first();
        if (is_null($globalRoleId) || !is_int($globalRoleId) || !is_string($this->email) || is_null($user)) {
            return true; // Une autre validation devrait échouer
        }

        $globalRoleUser = GlobalRoleUser::where('role_id', $globalRoleId)
            ->where('user_id', $user->id)
            ->first();

        return is_null($globalRoleUser) || $globalRoleUser->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.role_once_per_user');
    }
}
