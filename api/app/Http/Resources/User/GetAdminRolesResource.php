<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\GetRoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetAdminRolesResource extends Resource
{
    protected $lanRoles;

    public function __construct(Collection $globalRoles, Collection $lanRoles)
    {
        $this->lanRoles = $lanRoles;
        parent::__construct($globalRoles);
    }

    /**
     * Transformer la ressource en tableau.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'global_roles' => GetRoleResource::collection($this),
            'lan_roles' => GetRoleResource::collection($this->lanRoles),
        ];
    }
}
