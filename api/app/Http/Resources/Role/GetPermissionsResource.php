<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;

class GetPermissionsResource extends Resource
{
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
            'id' => intval($this->id),
            'name' => $this->name,
            'can_be_per_lan' => (bool)$this->can_be_per_lan,
            'display_name' => trans('permission.display-name-' . $this->name),
            'description' => trans('permission.description-' . $this->name),
        ];
    }
}
