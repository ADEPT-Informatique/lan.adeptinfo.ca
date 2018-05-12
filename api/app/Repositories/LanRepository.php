<?php


namespace App\Repositories;


use App\Model\Lan;
use DateTime;

interface LanRepository
{
    public function createLan(
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $reservationStart,
        DateTime $tournamentStart,
        string $eventKeyId,
        string $publicKeyId,
        string $secretKeyId,
        int $price
    ): Lan;
}