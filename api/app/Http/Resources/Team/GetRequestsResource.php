<?php

namespace App\Http\Resources\Team;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetRequestsResource extends JsonResource
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
            'team_id' => intval($this->team_id),
            'team_tag' => $this->team_tag,
            'team_name' => $this->team_name,
            'tournament_id' => intval($this->tournament_id),
            'tournament_name' => $this->tournament_name,
        ];
    }
}
