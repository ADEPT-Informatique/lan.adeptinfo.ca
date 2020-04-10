<?php

namespace App\Rules\Team;

use App\Model\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;

/**
 * Un tag de joueur appartient à un utilisateur.
 *
 * Class TagBelongsToUser
 */
class TagBelongsToUser implements Rule
{
    protected $userId;

    /**
     * TagBelongsToUser constructor.
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
     * @param mixed  $tagId     Id du tag
     *
     * @throws AuthorizationException
     *
     * @return bool
     */
    public function passes($attribute, $tagId): bool
    {
        $tag = null;

        /*
         * Conditions de garde :
         * L'id du tag est un entier
         * L'id de l'utilisateur est un entier
         * Un tag de joueur doit correspondre à l'id de tag de joueur
         */
        if (
            !is_int($tagId) ||
            !is_int($this->userId) ||
            is_null($tag = Tag::find($tagId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // L'id d'utilisateur du tag ne correspond pas à celui de l'utilisateur, lancer une exception
        if ($tag->user_id != $this->userId) {
            throw new AuthorizationException(trans('validation.forbidden'));
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
        return trans('validation.tag_belongs_to_user');
    }
}
