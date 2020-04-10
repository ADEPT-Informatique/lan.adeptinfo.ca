<?php

namespace App\Rules\User;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur possède une permission dans un rôle global.
 *
 * Class HasPermission
 */
class HasPermission implements Rule
{
    protected $userId;

    /**
     * HasPermission constructor.
     *
     * @param string $userId Id de l'utilisateur
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $permission Nom de la permission
     *
     * @throws AuthorizationException
     *
     * @return bool
     */
    public function passes($attribute, $permission): bool
    {
        /*
         * Conditions de garde :
         * L'id de l'utilisateur est un entier
         * La permission est une chaîne de caractères
         * Le nom de la permission n'est pas nul
         * L'id de l'utilisateur n'est pas nul
         */
        if (
            !is_int($this->userId) ||
            !is_string($permission) ||
            is_null($permission) ||
            is_null($this->userId)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher si l'utilisateur possède la permission dans un rôle global
        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $this->userId)
            ->where('permission.name', $permission)
            ->get();

        // Compte le nombre de lien entre la permission et l'utilisateur
        $hasPermission = $globalPermissions->unique()->count() > 0;

        // Si aucun lien n'existe, lancer une exception
        if (!$hasPermission) {
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return $hasPermission;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.has_permission');
    }
}
