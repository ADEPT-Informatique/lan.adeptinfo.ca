<?php

namespace App\Http\Resources\Contribution;

use App\Model\User;
use Illuminate\Http\Request;

/**
 * @property int id
 * @property string user_full_name
 * @property int user_id
 */
class ContributionResource extends Resource
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
        // Déterminer le type de contribution
        $userFullName = !is_null($this->user_full_name) ?
            $this->user_full_name :
            User::find($this->user_id)->getFullName();

        return [
            'id' => intval($this->id),
            'user_full_name' => $userFullName,
        ];
    }
}
