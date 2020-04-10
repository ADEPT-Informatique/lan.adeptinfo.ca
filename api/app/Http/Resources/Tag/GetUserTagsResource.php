<?php

namespace App\Http\Resources\Tag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetUserTagsResource extends JsonResource
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
            'tag_id' => intval($this->tag_id),
            'tag_name' => $this->tag_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'is_leader' => $this->is_leader ? true : false,
        ];
    }
}
