<?php

namespace App\Rules\Role;

use App\Model\LanRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur possède une permission.
 *
 * Class HasPermissionInLan
 */
class HasPermissionInLan implements Rule
{
    protected $roleId;
    protected $userId;

    /**
     * HasPermissionInLan constructor.
     *
     * @param null $roleId Id du rôle  global
     * @param null $userId Id de l'utilisateur
     */
    public function __construct($roleId, $userId)
    {
        $this->roleId = $roleId;
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param string $permissionName Nom de la permission
     *
     * @throws AuthorizationException
     *
     * @return bool
     */
    public function passes($attribute, $permissionName): bool
    {
        $lanRole = null;
        /*
         * Conditions de garde :
         * Le nom de la permission n'est pas nul
         * L'id de l'utilisateur est un entier
         * L'id du rôle est un entier
         * Le rôle de LAN existe
         * L'id de l'utilisateur n'est pas nul
         */
        if (
            is_null($permissionName) ||
            !is_int($this->roleId) ||
            !is_int($this->userId) ||
            is_null($lanRole = LanRole::find($this->roleId)) ||
            is_null($this->userId)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Rechercher si l'utilisateur possède la permission dans l'un de ses rôles de LAN
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lanRole->lan_id)
            ->where('lan_role_user.user_id', $this->userId)
            ->where('permission.name', $permissionName)
            ->get();

        // Rechercher si l'utilisateur possède la permission dans l'un de ses rôles globaux
        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $this->userId)
            ->where('permission.name', $permissionName)
            ->get();

        // Fusionner les 2 listes de permission trouvées
        // Si aucune permission n'a été trouvée, une erreur d'autorisation est lancée
        $hasPermission = $lanPermissions->merge($globalPermissions)->unique()->count() > 0;
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
