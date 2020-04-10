<?php

namespace App\Rules\Role;

use App\Model\LanRole;
use App\Model\PermissionLanRole;
use Illuminate\Contracts\Validation\Rule;

/**
 * Des permission ne sont pas liées à un rôle de LAN.
 *
 * Class PermissionsDontBelongToLanRole
 */
class PermissionsDontBelongToLanRole implements Rule
{
    protected $roleId;

    /**
     * PermissionsDontBelongToLanRole constructor.
     *
     * @param null $roleId Id du rôle de LAN
     */
    public function __construct($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param array  $permissionIds
     *
     * @return bool
     */
    public function passes($attribute, $permissionIds): bool
    {
        $lanRole = null;

        /*
         * Conditions de garde :
         * Les ids de permission ne sont pas nuls
         * L'id du rôle est un entier
         * Les ids de permissions sont un tableau
         * L'id du rôle de LAN correspond à un rôle de LAN existant
         */
        if (
            is_null($permissionIds) ||
            !is_int($this->roleId) ||
            !is_array($permissionIds) ||
            is_null($lanRole = LanRole::find($this->roleId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Pour chaque id de permission passé
        foreach ($permissionIds as $permissionId) {

            // Chercher lien entre le rôle et l'id de la permission
            $permission = PermissionLanRole::where('permission_id', $permissionId)
                ->where('role_id', $lanRole->id)
                ->get()
                ->first();

            // Si un lien est trouvé
            if (!is_null($permission)) {
                // Le test échoue
                return false;
            }
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
        return trans('validation.permissions_dont_belong_to_user');
    }
}
