<?php

namespace App\Rules\Role;

use App\Model\GlobalRole;
use App\Model\PermissionGlobalRole;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un lien existe entre des permissions et un rôle global.
 *
 * Class PermissionsBelongToGlobalRole
 */
class PermissionsBelongToGlobalRole implements Rule
{
    protected $roleId;

    /**
     * PermissionsDontBelongToGlobalRole constructor.
     *
     * @param null $roleId Id du rôle global
     */
    public function __construct($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param array  $permissionIds Id des permissions
     *
     * @return bool
     */
    public function passes($attribute, $permissionIds): bool
    {
        $globalRole = null;

        /*
         * Conditions de garde :
         * Les permissions ne sont pas nul
         * Les permissions sont un tableau
         * L'id du rôle est un entier
         * L'id du rôle correspond à un rôle qui existe
         */
        if (
            is_null($permissionIds) ||
            !is_array($permissionIds) ||
            !is_int($this->roleId) ||
            is_null($globalRole = GlobalRole::find($this->roleId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Pour chaque id de permission
        foreach ($permissionIds as $permissionId) {
            // Chercher un lien entre la permission et le rôle global
            $permission = PermissionGlobalRole::where('permission_id', $permissionId)
                ->where('role_id', $globalRole->id)
                ->get()
                ->first();

            // Si aucune permission n'est trouvée, la validation échoue
            if (is_null($permission)) {
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
        return trans('validation.permissions_belong_to_user');
    }
}
