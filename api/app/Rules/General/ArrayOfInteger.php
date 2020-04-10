<?php

namespace App\Rules\General;

use Illuminate\Contracts\Validation\Rule;

/**
 * Les éléments du tableau passé est constitué uniquement d'entiers positifs.
 *
 * Class ArrayOfInteger
 */
class ArrayOfInteger implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param array  $array     tableau d'entiers positifs
     *
     * @return bool
     */
    public function passes($attribute, $array): bool
    {
        /*
         * Conditions de garde :
         * L'élément passé est non nul
         * L'élément passé doit être un tableau
         */
        if (is_null($array) || !is_array($array)) {
            return true; // Une autre validation devrait échouer
        }

        foreach ($array as $v) {
            if (!is_int($v) || $v <= 0) {
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
        return trans('validation.array_of_integer');
    }
}
