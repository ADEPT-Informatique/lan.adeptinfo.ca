<?php

namespace App\Rules\Contribution;

use App\Model\Contribution;
use App\Model\ContributionCategory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur possède une permission dans un LAN pour une contribution.
 *
 * Class HasPermissionInLan
 */
class HasPermissionInLan implements Rule
{
    protected $contributionId;
    protected $userId;

    /**
     * HasPermissionInLan constructor.
     *
     * @param null $contributionId Id de la contribution.
     * @param null $userId         Id de l'utilisateur
     */
    public function __construct($contributionId, $userId)
    {
        $this->contributionId = $contributionId;
        $this->userId = $userId;
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
        $contribution = null;
        $contributionCategory = null;
        /*
         * Conditions de garde :
         * Le nom de la permission n'est pas nul
         * L'id de la contribution est un entier
         * Une contribution correspond à l'id de la contribution
         * Une catégorie de contribution existe pour la contribution
         * Un utilisateur correspond à l'id de l'utilisateur
         */
        if (
            is_null($permission) ||
            !is_int($this->contributionId) ||
            is_null($contribution = Contribution::find($this->contributionId)) ||
            is_null($contributionCategory = ContributionCategory::find($contribution->contribution_category_id)) ||
            is_null($this->userId)
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Rechercher si l'utilisateur possède la permission dans l'un de ses rôles de LAN
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $contributionCategory->lan_id)
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

        // Si l'utilisateur ne possède pas la permission et ne fait pas parti de l'équipe d'organisation du tournoi
        if (!$hasPermission) {
            // Lancer une exception
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return $hasPermission;
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
