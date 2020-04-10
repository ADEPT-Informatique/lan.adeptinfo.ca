<?php

namespace App\Rules\Tournament;

use App\Model\OrganizerTournament;
use App\Model\Tournament;
use Illuminate\{Contracts\Validation\Rule};

/**
 * Un utilisateur est administrateur d'un tournoi.
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
     * @param mixed  $tournamentId
     *
     * @return bool
     */
    public function passes($attribute, $tournamentId): bool
    {
        $tournament = null;

        /*
         * Conditions de garde :
         * L'id de l'utilisateur est un entier
         * L'id du tournoi est un entier
         * L'id du tournoi correspond à un tournoi
         */
        if (
            !is_int($this->userId) ||
            !is_int($tournamentId) ||
            is_null(Tournament::find($tournamentId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher s'il existe un lien entre l'utilisateur et le tournoi
        return OrganizerTournament::where('organizer_id', $this->userId)
                ->where('tournament_id', $tournamentId)
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
