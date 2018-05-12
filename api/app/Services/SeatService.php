<?php

namespace App\Services;


use App\Model\Reservation;

interface SeatService
{
    public function book(string $lanId, string $seatId): Reservation;
}