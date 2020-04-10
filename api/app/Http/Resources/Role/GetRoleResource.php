<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;

class GetRoleResource extends Resource
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
        switch (app('translator')->getLocale()) {
            case 'fr':
                return [
                    'id' => intval($this->id),
                    'name' => $this->name,
                    'display_name' => $this->fr_display_name,
                    'description' => $this->fr_description,
                ];
            case 'en':
                return [
                    'id' => intval($this->id),
                    'name' => $this->name,
                    'display_name' => $this->en_display_name,
                    'description' => $this->en_description,
                ];
            default:
                return [
                    'id' => intval($this->id),
                    'name' => $this->name,
                    'display_name' => $this->fr_display_name,
                    'description' => $this->fr_description,
                ];
        }
    }
}
