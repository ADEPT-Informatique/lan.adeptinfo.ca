<?php

namespace App\Repositories;


use App\Model\Lan;
use App\Model\Reservation;
use Illuminate\Contracts\Auth\Authenticatable;

interface SeatRepository
{
    public function attachLanUser(Authenticatable $user, Lan $lan, string $seatId): void;

    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation;

    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation;
}