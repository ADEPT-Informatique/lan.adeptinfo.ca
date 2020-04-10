<?php

namespace App\Rules\Team;

use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur est le chef d'une équipe.
 *
 * Class UserIsTeamLeaderTeam
 */
class UserIsTeamLeaderTeam implements Rule
{
    protected $userId;

    /**
     * UserIsTeamLeaderTeam constructor.
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
     * @param mixed  $teamId
     *
     * @throws AuthorizationException
     *
     * @return bool
     */
    public function passes($attribute, $teamId): bool
    {
        $team = null;

        /*
         * Condition de garde :
         * L'id de l'utilisateur est un entier
         * L'id de l'équipe est un entier
         * L'id de l'équipe doit correspondre à l'id d'une équipe
         */
        if (
            !is_int($this->userId) ||
            !is_int($teamId) ||
            is_null($team = Team::find($teamId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher les tags de joueur de l'utilisateur courant
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', $this->userId)
            ->pluck('id')
            ->toArray();

        // Chercher s'il existe un lien entrer l'un des tags de l'utilisateur courant, l'équipe, et s'il est le chef
        $isInTeam = TagTeam::whereIn('tag_id', $tagIds)
                ->where('team_id', $team->id)
                ->where('is_leader', true)
                ->count() > 0;

        // Lancer une exception si aucun lien n'a été trouvé
        if (!$isInTeam) {
            throw new AuthorizationException(trans('validation.forbidden'));
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
        return trans('validation.user_is_team_leader');
    }
}
