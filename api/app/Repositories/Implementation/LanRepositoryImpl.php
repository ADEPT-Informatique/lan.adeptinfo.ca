<?php


namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Repositories\LanRepository;
use DateTime;

class LanRepositoryImpl implements LanRepository
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
    ): Lan
    {
        $lan = new Lan();
        $lan->lan_start = $lanStart->format('Y-m-d\TH:i:s');
        $lan->lan_end = $lanEnd->format('Y-m-d\TH:i:s');
        $lan->reservation_start = $reservationStart->format('Y-m-d\TH:i:s');
        $lan->tournament_start = $tournamentStart->format('Y-m-d\TH:i:s');
        $lan->event_key_id = $eventKeyId;
        $lan->public_key_id = $publicKeyId;
        $lan->secret_key_id = $secretKeyId;
        $lan->price = $price;
        $lan->save();

        return $lan;
    }
}