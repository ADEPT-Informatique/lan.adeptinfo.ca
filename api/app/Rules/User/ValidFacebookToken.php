<?php

namespace App\Rules\User;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un token Facebook est valide.
 *
 * Class ValidFacebookToken
 */
class ValidFacebookToken implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $token     Token Facebook
     *
     * @return bool
     */
    public function passes($attribute, $token): bool
    {
        /*
         * Condition de garde :
         * Le token est une chaîne de caractères
         */
        if (!is_string($token)) {
            return true; // Une autre validation devrait échouer
        }

        try {
            // Essayer d'obtenir les informations de l'utilisateur avec le token
            FacebookUtils::getFacebook()->get(
                '/me?fields=id,first_name,last_name,email',
                $token
            );
        } catch (FacebookSDKException $e) {
            // Si une erreur est envoyée, c'est que le token n'est pas valide
            return false;
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
        return trans('validation.valid_facebook_token');
    }
}
