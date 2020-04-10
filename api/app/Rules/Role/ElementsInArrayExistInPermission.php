<?php

namespace App\Rules\Role;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Class ElementsInArrayExistInPermission.
 */
class ElementsInArrayExistInPermission implements Rule
{
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
        /*
         * Conditions de garde :
         * L'élément passé est non nul
         * L'élément passé est un tableau
         */
        if (is_null($permissionIds) || !is_array($permissionIds)) {
            return true; // Une autre validation devrait échouer
        }

        // Pour chaque id de permission du tableau
        foreach ($permissionIds as $permissionId) {
            // Si une permission est trouvée
            if (is_null(DB::table('permission')->find($permissionId))) {
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
        return trans('validation.elements_in_array_exist_in_permission');
    }
}
