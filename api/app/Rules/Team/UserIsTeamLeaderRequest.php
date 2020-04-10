<?php

namespace App\Rules\Team;

use App\Model\Request;
use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur est le chef de l'équipe d'une requête.
 *
 * Class UserIsTeamLeaderRequest
 */
class UserIsTeamLeaderRequest implements Rule
{
    protected $userId;

    /**
     * UserIsTeamLeaderRequest constructor.
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
     * @param mixed  $requestId Id de la requête pour entrer dans l'équipe
     *
     * @throws AuthorizationException
     *
     * @return bool
     */
    public function passes($attribute, $requestId): bool
    {
        $request = null;
        $team = null;

        /*
         * Conditions de garde :
         * L'id de la requête est un entier
         * La valeur en entier de la variable n'est pas à 0
         * L'id de l'utilisateur est un entier
         * L'id de la requête pour entrer dans l'équipe correspond à une requête pour entrer dans une équipe
         * Une équipe correspond à l'id de l'équipe de la requête
         */
        if (
            !is_int((int) $requestId) ||
            intval((int) $requestId) == 0 ||
            !is_int($this->userId) ||
            is_null($request = Request::find($requestId)) ||
            is_null($team = Team::find($request->team_id))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher les tags de joueur de l'utilisateur courant
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', $this->userId)
            ->pluck('id')
            ->toArray();

        // Chercher s'il existe un lien entrer l'un des tags de l'utilisateur courant, l'équipe de la requête,
        // et s'il est le chef
        $isLeaderInTeam = TagTeam::whereIn('tag_id', $tagIds)
                ->where('team_id', $team->id)
                ->where('is_leader', true)
                ->count() > 0;

        // Lancer une exception si aucun lien n'a été trouvé
        if (!$isLeaderInTeam) {
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return $isLeaderInTeam;
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
