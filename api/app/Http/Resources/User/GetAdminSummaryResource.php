<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\GetPermissionsSummaryResource;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetAdminSummaryResource extends Resource
{
    protected $permissions;
    protected $hasTournaments;

    public function __construct(Authenticatable $resource, bool $hasTournaments, Collection $permissions)
    {
        $this->permissions = $permissions;
        $this->hasTournaments = $hasTournaments;
        parent::__construct($resource);
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'has_tournaments' => $this->hasTournaments,
            'permissions' => GetPermissionsSummaryResource::collection($this->permissions),
        ];
    }
}
