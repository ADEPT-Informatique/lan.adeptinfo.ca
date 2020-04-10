<?php

namespace App\Rules\Team;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Le nom d'un tag d'équipe n'est déjà utilisé dans un tournoi.
 *
 * Class UniqueTeamTagPerTournament
 */
class UniqueTeamTagPerTournament implements Rule
{
    protected $tournamentId;

    /**
     * UniqueTeamTagPerTournament constructor.
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
     * @param mixed  $name
     *
     * @return bool
     */
    public function passes($attribute, $name): bool
    {
        /*
         * Conditions de validation
         * L'id du tournoi est un entier
         * Le nom du tag est une chaîne de caractères
        */

        if (
            !is_int($this->tournamentId) ||
            !is_string($name)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher si des équipes ont le tag du nom spécifié dans le tournoi
        return DB::table('team')
                ->where('tournament_id', $this->tournamentId)
                ->where('tag', $name)
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
        return trans('validation.unique_team_tag_per_tournament');
    }
}
