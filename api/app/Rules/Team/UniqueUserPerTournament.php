<?php

namespace App\Rules\Team;

use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur n'est qu'une seule fois dans un tournoi.
 *
 * Class UniqueUserPerTournament
 */
class UniqueUserPerTournament implements Rule
{
    protected $userId;

    /**
     * UniqueUserPerTournament constructor.
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
     * @param mixed  $teamId    Id de l'équipe du tournoi
     *
     * @return bool
     */
    public function passes($attribute, $teamId): bool
    {
        $team = null;

        /*
         * Condition de garde :
         * L'id de l'équipe correspond à une équipe
         * L'id de l'utilisateur
         * L'id du tournoi correspond à un tournoi
         */
        if (
            !is_int($teamId) ||
            !is_int($this->userId) ||
            is_null($team = Team::find($teamId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // du tournoi de l'équipe
        $tournamentId = $team->tournament_id;

        // Chercher les équipes du tournoi de l'équipe
        $teamIds = DB::table('team')
            ->select('id')
            ->where('tournament_id', $tournamentId)
            ->pluck('id')
            ->toArray();

        // Chercher les tags de joueur des équipes du tournoi
        $tagIds = DB::table('tag_team')
            ->select('tag_id')
            ->whereIn('team_id', $teamIds)
            ->pluck('tag_id')
            ->toArray();

        // Si l'utilisateur courant fait parti des tags de joueur du tournoi de l'équipe
        return DB::table('tag')
                ->select('id')
                ->whereIn('id', $tagIds)
                ->where('user_id', $this->userId)
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_user_per_tournament');
    }
}
