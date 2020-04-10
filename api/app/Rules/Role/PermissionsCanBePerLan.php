<?php

namespace App\Rules\Role;

use App\Model\Permission;
use Illuminate\Contracts\Validation\Rule;

class PermissionsCanBePerLan implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param array  $permissionsIds
     *
     * @return bool
     */
    public function passes($attribute, $permissionsIds): bool
    {
        /*
         * Conditions de garde :
         * Les permissions ne sont pas nulles
         * Les permissions sont un tableau
         */
        if (is_null($permissionsIds) || !is_array($permissionsIds)) {
            return true; // Une autre validation devrait échouer
        }

        $permission = null;
        // Pour chaque id de permission
        foreach ($permissionsIds as $permissionId) {

            // Si aucune permission n'est trouvée, quitter la validation, une autre validation devrait échouer
            if (is_null($permission = Permission::find($permissionId))) {
                return true;
            }

            // Si la permission ne peut être par LAN
            if (!$permission->can_be_per_lan) {
                // La validation échoue.
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
        return trans('validation.permissions_can_be_per_lan');
    }
}
