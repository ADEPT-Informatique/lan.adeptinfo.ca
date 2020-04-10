<?php

namespace App\Rules\Team;

use App\Model\OrganizerTournament;
use App\Model\Team;
use Illuminate\{Contracts\Validation\Rule};

/**
 * Un utilisateur fait parti de l'éqipe d'administrateur du tournoi d'une équipe.
 *
 * Class UserIsTournamentAdmin
 */
class UserIsTournamentAdmin implements Rule
{
    protected $userId;

    /**
     * UserIsTournamentAdmin constructor.
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
     * @return bool
     */
    public function passes($attribute, $teamId): bool
    {
        $team = null;

        /*
         * Conditions de garde :
         * L'id de l'utilisateur est un entier
         * L'id de l'équipe est un entier
         * L'id de l'équipe correspond à l'id d'une équipe
         */
        if (
            !is_int($this->userId) ||
            !is_int($teamId) ||
            is_null($team = Team::find($teamId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher s'il existe un lien entre l'utilisateur courant et le tournoi de l'équipe
        return OrganizerTournament::where('organizer_id', $this->userId)
                ->where('tournament_id', $team->tournament_id)
                ->count() > 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.organizer_has_tournament');
    }
}
