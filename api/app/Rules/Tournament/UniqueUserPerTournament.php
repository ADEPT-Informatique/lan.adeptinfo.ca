<?php

namespace App\Rules\Tournament;

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
     * @param int $userId Id de l'uitilisateur
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $tournamentId
     *
     * @return bool
     */
    public function passes($attribute, $tournamentId): bool
    {
        /*
         * Condition de garde :
         * L'id du tournoi est un entier
         * L'id de l'utilisateur est un entier
         * L'id du tournoi n'est pas nul
         */
        if (
            !is_int($this->userId) ||
            !is_int($tournamentId) ||
            is_null($tournamentId)
        ) {
            return true; // Une autre validation devrait échouer
        }

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
