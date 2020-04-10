<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Reservation\GetUserDetailsReservationResource;
use App\Model\Reservation;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetUserDetailsResource extends Resource
{
    protected $currentSeat;
    protected $seatHistory;

    public function __construct(User $resource, ?Reservation $currentSeat, ?Collection $seatHistory)
    {
        $this->currentSeat = $currentSeat;
        $this->seatHistory = $seatHistory;
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
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'current_place' => $this->currentSeat != null ? $this->currentSeat->seat_id : null,
            'place_history' => GetUserDetailsReservationResource::collection($this->seatHistory),
        ];
    }
}
