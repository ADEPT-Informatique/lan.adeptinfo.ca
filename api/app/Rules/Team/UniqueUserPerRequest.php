<?php

namespace App\Rules\Team;

use App\Model\Tag;
use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Une requête n'existe qu'une fois par utilisateur.
 *
 * Class UniqueUserPerRequest
 */
class UniqueUserPerRequest implements Rule
{
    protected $tagId;
    protected $userId;

    /**
     * UniqueUserPerRequest constructor.
     *
     * @param int $tagId  Id du tag de joueur
     * @param int $userId
     */
    public function __construct($tagId, $userId)
    {
        $this->tagId = $tagId;
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $teamId    Id de l'équipe
     *
     * @return bool
     */
    public function passes($attribute, $teamId): bool
    {
        $tag = null;
        $team = null;

        /*
         * Conditions de garde :
         * L'id du tag est un entier
         * L'id de l'utilisateur est un entier
         * L'id de l'équipe est un entier
         * L'id du tag correspond à un tag
         * L'id de l'équipe correspond à une équipe
         * L'id de l'utilisateur du tag correspond à celui de l'utilisateur courant
         * L'id de l'équipe correspond à une équipe
         */
        if (
            !is_int($this->tagId) ||
            !is_int($this->userId) ||
            !is_int($teamId) ||
            is_null($tag = Tag::find($this->tagId)) ||
            is_null($team = Team::find($teamId)) ||
            $tag->user_id != $this->userId ||
            is_null($team)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher les tag de l'utilisateur courant
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', $this->userId)
            ->pluck('id')
            ->toArray();

        // Chercher si des requêtes ont l'un des id de tag de l'utilisateur pour l'équipe
        return DB::table('request')
                ->whereIn('id', $tagIds)
                ->where('team_id', $team->id)
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_user_per_request');
    }
}
