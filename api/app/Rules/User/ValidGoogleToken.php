<?php

namespace App\Rules\User;

use Exception;
use Google_Client;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un token Google est valide.
 *
 * Class ValidGoogleToken
 */
class ValidGoogleToken implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $idToken
     *
     * @return bool
     */
    public function passes($attribute, $idToken): bool
    {
        /*
         * Conditions de garde :
         * Le token est une chaîne de caractères
         */
        if (!is_string($idToken)) {
            return true; // Une autre validation devrait échouer
        }

        // Créer client google
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $token = null;

        try {
            // Vérifier le token
            $token = $client->verifyIdToken($idToken);
        } catch (Exception $e) {
            // Si le token n'est pas valide, une exception est lancée
            return false;
        }

        if (is_bool($token)) {
            return $token;
        } else {
            return true;
        }
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.valid_google_token');
    }
}
