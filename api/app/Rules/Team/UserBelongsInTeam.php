<?php

namespace App\Rules\Team;

use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Auth\Access\AuthorizationException as AuthorizationExceptionAlias;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur fait parti d'une équipe.
 *
 * Class UserBelongsInTeam
 */
class UserBelongsInTeam implements Rule
{
    protected $userId;

    /**
     * UserBelongsInTeam constructor.
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
     * @param mixed  $teamId    Id de l'équipe
     *
     * @throws AuthorizationExceptionAlias
     *
     * @return bool
     */
    public function passes($attribute, $teamId): bool
    {
        $team = null;

        /*
         * L'id de l'équipe est un entier
         * L'id de l'utilisateur est un entier
         * L'id de l'équipe correspond à une équipe
         */
        if (
            !is_int($teamId) ||
            !is_int($this->userId) ||
            is_null($team = Team::find($teamId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher les tags de l'utilisateur courant
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', $this->userId)
            ->pluck('id')
            ->toArray();

        // Parmi les tags du joueur, chercher si un tag a un lien avec l'équipe
        $isInTeam = TagTeam::whereIn('tag_id', $tagIds)
                ->where('team_id', $team->id)
                ->count() > 0;

        // Lancer une exception si aucun lien n'a été trouvé
        if (!$isInTeam) {
            throw new AuthorizationExceptionAlias(trans('validation.forbidden'));
        }

        return $isInTeam;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.user_belongs_in_team');
    }
}
