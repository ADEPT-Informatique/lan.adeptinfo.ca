<?php

namespace App\Rules\Team;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un nom n'existe pas encore comme nom d'équipe dans un tournoi.
 *
 * Class UniqueTeamNamePerTournament
 */
class UniqueTeamNamePerTournament implements Rule
{
    protected $tournamentId;

    /**
     * UniqueTeamNamePerTournament constructor.
     *
     * @param int $tournamentId Id du tournoi
     */
    public function __construct($tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $teamName  Nom
     *
     * @return bool
     */
    public function passes($attribute, $teamName): bool
    {
        /*
         * Conditions de garde :
         * L'id du tournoi est un entier
         * Le nom de l'équipe est une chaîne de caractères
         */
        if (
            !is_int($this->tournamentId) ||
            !is_string($teamName)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher si des équipes portent le nom passé, pour le tournoi
        return DB::table('team')
                ->where('tournament_id', $this->tournamentId)
                ->where('name', $teamName)
                ->whereNull('deleted_at')
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_team_name_per_tournament');
    }
}
