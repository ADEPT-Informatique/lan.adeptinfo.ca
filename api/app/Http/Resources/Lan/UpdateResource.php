<?php

namespace App\Http\Resources\Lan;

use App\Model\Lan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UpdateResource extends Resource
{
    protected $reservedPlaces;
    protected $images;

    public function __construct(Lan $resource, int $reservedPlaces, Collection $images)
    {
        $this->reservedPlaces = $reservedPlaces;
        $this->images = $images;
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
            'id' => $this->id,
            'name' => $this->name,
            'lan_start' => $this->lan_start,
            'lan_end' => $this->lan_end,
            'seat_reservation_start' => $this->seat_reservation_start,
            'tournament_reservation_start' => $this->tournament_reservation_start,
            'longitude' => floatval(number_format($this->longitude, 7)),
            'latitude' => floatval(number_format($this->latitude, 7)),
            'event_key' => $this->event_key,
            'places' => [
                'reserved' => $this->reservedPlaces,
                'total' => $this->places,
            ],
            'price' => $this->price,
            'rules' => $this->rules,
            'description' => $this->description,
            'images' => $this->images,
        ];
    }
}
