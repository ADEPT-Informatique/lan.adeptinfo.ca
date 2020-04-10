<?php

namespace App\Rules\User;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un utilisateur Facebook donne la permission à l'API d'accéder à son courriel.
 *
 * Class FacebookEmailPermission
 */
class FacebookEmailPermission implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $token     Token de connection Facebook de l'utilisateur
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

        $response = null;

        try {
            // Tenter d'obtenir les informations (y comprit le courriel) de l'utilisateur à partir de son token
            $response = FacebookUtils::getFacebook()->get(
                '/me?fields=id,first_name,last_name,email',
                $token
            );
        } catch (FacebookSDKException $e) {
            // Une autre validation devrait échouer
            return true;
        }
        // Vérifier qu'un courriel a bien été renvoyé
        return array_key_exists('email', $response->getDecodedBody());
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.facebook_email_permission');
    }
}
