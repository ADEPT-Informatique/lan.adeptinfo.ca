<?php

namespace App\Rules\User;

use Illuminate\Contracts\Validation\Rule;

/**
 * Un courriel ne correspond pas à celui de l'utilisateur courant.
 *
 * Class EmailNotCurrentUser
 */
class EmailNotCurrentUser implements Rule
{
    protected $currentUserEmail;

    /**
     * EmailNotCurrentUser constructor.
     *
     * @param $currentUserEmail string courriel de l'utilisateur courant
     */
    public function __construct($currentUserEmail)
    {
        $this->currentUserEmail = $currentUserEmail;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $email     Courriel à valider
     *
     * @return bool
     */
    public function passes($attribute, $email): bool
    {
        /*
         * Condition de garde :
         * Le courriel est une chaîne de caractères
         */
        if (!is_string($email)) {
            return true; // Une autre validation devrait échouer
        }

        return $email != $this->currentUserEmail;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.email_not_current_user');
    }
}
