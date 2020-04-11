<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetPermissionsSummaryResource extends JsonResource
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
        ];
    }
}
