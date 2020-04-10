<?php

namespace App\Http\Resources\Lan;

use Illuminate\Http\Request;

class ImageResource extends Resource
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
            'id' => $this->id,
            'image' => $this->image,
        ];
    }
}
