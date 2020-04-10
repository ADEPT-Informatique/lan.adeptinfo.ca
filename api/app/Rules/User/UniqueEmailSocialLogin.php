<?php

namespace App\Rules\User;

use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

/**
 * Valider que si le courriel est nouveau.
 * S'il est déjà utilisé, valider qu'il utilise une connexion sociale (Facebook ou Google),
 * et qu'il n'est pas en attente de confirmation.
 *
 * Class UniqueEmailSocialLogin
 */
class UniqueEmailSocialLogin implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $courriel  Courriel de l'utilisateur
     *
     * @return bool
     */
    public function passes($attribute, $courriel): bool
    {
        $user = null;

        /*
         * Condition de garde :
         * Un utilisateur correspond au courriel
         * Le courriel est une chaîne de caractères
         */
        if (is_null($user = User::where('email', $courriel)->first()) || !is_string($courriel)) {
            return true; // Une autre validation devrait échouer
        }

        // Si l'utilisateur est inscrit par un réseau social
        $hasSocialLogin = !is_null($user->facebook_id) || !is_null($user->google_id);

        // Si l'utilisateur est confirmé dans l'application
        $hasConfirmationCode = !is_null($user->confirmation_code);

        // La validation passe si l'utilisateur est inscrit avec un réseau social
        // et s'il n'a pas de code de confirmation
        return $hasSocialLogin && !$hasConfirmationCode;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_email_social_login');
    }
}
