<?php

namespace App\Rules\Team;

use App\Model\Request;
use App\Model\Tag;
use Illuminate\{Contracts\Validation\Rule};

/**
 * Une requête appartient à un utilisateur.
 *
 * Class RequestBelongsToUser
 */
class RequestBelongsToUser implements Rule
{
    protected $userId;

    /**
     * RequestBelongsToUser constructor.
     *
     * @param int $userId Id de l'utilisateur
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $requestId Id de la requête
     *
     * @return bool
     */
    public function passes($attribute, $requestId): bool
    {
        $request = null;
        $tag = null;

        /*
         * Conditions de garde :
         * L'id de l'utilisateur est un entier
         * L'id de la requête est un entier
         * Une requête existe pour l'id de requête passée
         * L'id de la requête est un entier
         * Un tag de joueur existe pour la requête passée
         */
        if (
            !is_int($requestId) ||
            is_null($request = Request::find($requestId)) ||
            !is_int($request->tag_id) ||
            is_null($tag = Tag::find($request->tag_id))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // L'id de l'utilisateur du tag de la requête correspond à l'id de l'utilisateur passé
        return $tag->user_id == $this->userId;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.request_belongs_to_user');
    }
}
