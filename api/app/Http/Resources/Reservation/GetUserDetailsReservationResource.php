<?php

namespace App\Http\Resources\Reservation;

use App\Model\Lan;
use Illuminate\Http\Request;

class GetUserDetailsReservationResource extends Resource
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
        $lan = Lan::find($this->lan_id);

        return [
            'seat_id' => $this->seat_id,
            'lan' => $lan->name,
            'reserved_at' => $this->created_at->format('Y-m-d H:i:s'),
            'arrived_at' => $this->arrived_at == '0000-00-00 00:00:00' ? null : $this->arrived_at,
            'left_at' => $this->left_at == '0000-00-00 00:00:00' ? null : $this->left_at,
            'canceled_at' => is_null($this->deleted_at) ? null : $this->deleted_at->format('Y-m-d H:i:s'),
        ];
    }
}
