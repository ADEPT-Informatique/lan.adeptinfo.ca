<?php

namespace App\Rules\Tournament;

use App\Model\Lan;
use App\Model\OrganizerTournament;
use App\Model\Tournament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur possède une permission ou fait partie de l'équipe d'administrateurs du tournoi.
 *
 * Class HasPermissionInLanOrIsTournamentAdmin
 */
class HasPermissionInLanOrIsTournamentAdmin implements Rule
{
    protected $userId;
    protected $tournamentId;

    /**
     * HasPermissionInLanOrIsTournamentAdmin constructor.
     *
     * @param string $userId       Id de l'utilisateur
     * @param string $tournamentId Id du tournoi
     */
    public function __construct($userId, $tournamentId)
    {
        $this->userId = $userId;
        $this->tournamentId = $tournamentId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param string $attribute
     * @param mixed  $permission Nom de la permission
     *
     * @throws AuthorizationException
     *
     * @return bool
     */
    public function passes($attribute, $permission): bool
    {
        $tournament = null;
        $lan = null;

        /*
         * Conditions de garde
         * Le nom de la permission n'est pas nulle
         * La permission est une chaîne de caractères
         * L'id de l'utilisateur n'est pas nul
         * L'id de l'utilisateur est un entier
         * L'id du tournoi est un entier
         * L'id du tournoi correspond à un tournoi
         * L'id du LAN du tournoi correspond à un LAN
         */
        if (
            is_null($permission) ||
            !is_string($permission) ||
            is_null($this->userId) ||
            !is_int($this->userId) ||
            !is_int($this->tournamentId) ||
            is_null($tournament = Tournament::find($this->tournamentId)) ||
            is_null($lan = Lan::find($tournament->lan_id))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Rechercher si l'utilisateur possède la permission dans l'un de ses rôles de LAN
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lan->id)
            ->where('lan_role_user.user_id', $this->userId)
            ->where('permission.name', $permission)
            ->get();

        // Rechercher si l'utilisateur possède la permission dans l'un de ses rôles globaux
        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $this->userId)
            ->where('permission.name', $permission)
            ->get();

        // Fusionner les 2 listes de permission trouvées
        // Déterminer si l'utilisateur possède la permission
        $hasPermission = $lanPermissions->merge($globalPermissions)->unique()->count() > 0;

        // Déterminer si l'utilisateur fait parti de l'équipe d'organisateur du tournoi
        $isTournamentAdmin = OrganizerTournament::where('organizer_id', $this->userId)
                ->where('tournament_id', $this->tournamentId)
                ->count() > 0;

        // Si l'utilisateur ne possède pas la permission et ne fait pas parti de l'équipe d'organisation du tournoi
        if (!$hasPermission && !$isTournamentAdmin) {
            // Lancer une exception
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return $hasPermission || $isTournamentAdmin;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.has_permission');
    }
}
