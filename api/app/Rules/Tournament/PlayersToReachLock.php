<?php

namespace App\Rules\Tournament;

use App\Model\Team;
use App\Model\Tournament;
use Illuminate\Contracts\Validation\Rule;

/**
 * Des équipes n'on pas déjà commencées à s'inscrire à un tournoi.
 *
 * Class PlayersToReachLock
 */
class PlayersToReachLock implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $tournamentId Id du tournoi
     *
     * @return bool
     */
    public function passes($attribute, $tournamentId): bool
    {
        $tournament = null;

        /*
         * Condition de garde :
         * L'id du tournoi est un entier
         * L'id du tournoi correspond à un tournoi
         */
        if (
            !is_int($tournamentId) ||
            is_null($tournament = Tournament::find($tournamentId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Obtenir les nombre d'équipes d'un tournoi
        $teamsCount = Team::where('tournament_id', $tournament->id)
            ->count();

        // La validation passe si aucun équipe n'est encore inscrite
        return $teamsCount == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.players_to_reach_lock');
    }
}
