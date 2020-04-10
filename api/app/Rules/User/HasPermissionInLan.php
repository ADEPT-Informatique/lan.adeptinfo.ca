<?php

namespace App\Rules\User;

use App\Model\Lan;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Un utilisateur possède une permission.
 *
 * Class HasPermissionInLan
 */
class HasPermissionInLan implements Rule
{
    protected $lanId;
    protected $userId;

    /**
     * HasPermissionInLan constructor.
     *
     * @param string|null $lanId  Id du LAN pour les permissions par LAN
     * @param string      $userId Id de l'utilisateur
     */
    public function __construct($lanId, $userId)
    {
        $this->lanId = $lanId;
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
        /*
         * Conditions de garde :
         * L'id du LAN est un entier
         * L'id de l'utilisateur
         * La permission est une chaîne de caractères
         * Le nom de la permission n'est pas nul
         * Un LAN correspond à l'id du LAN
         * Un utilisateur correspond à l'id de l'utilisateur
         */
        if (
            !is_int($this->lanId) ||
            !is_int($this->userId) ||
            !is_string($permission) ||
            is_null($permission) ||
            is_null(Lan::find($this->lanId)) ||
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
            ->where('lan_role.lan_id', $this->lanId)
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
