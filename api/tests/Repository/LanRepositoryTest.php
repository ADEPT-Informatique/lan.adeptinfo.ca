<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LanRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $paramsContent = [
        'lan_start' => "2100-10-11T12:00:00",
        'lan_end' => "2100-10-12T12:00:00",
        'seat_reservation_start' => "2100-10-04T12:00:00",
        'tournament_reservation_start' => "2100-10-07T00:00:00",
        "event_key_id" => "123456789",
        "public_key_id" => "123456789",
        "secret_key_id" => "123456789",
        "price" => 0
    ];

    public function setUp()
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testSignUp()
    {
        $this->lanRepository->createLan(
            new DateTime($this->paramsContent['lan_start']),
            new DateTime($this->paramsContent['lan_end']),
            new DateTime($this->paramsContent['seat_reservation_start']),
            new DateTime($this->paramsContent['tournament_reservation_start']),
            $this->paramsContent['event_key_id'],
            $this->paramsContent['public_key_id'],
            $this->paramsContent['secret_key_id'],
            $this->paramsContent['price']
        );
        $this->seeInDatabase('lan', [
            'lan_start' => $this->paramsContent['lan_start'],
            'lan_end' => $this->paramsContent['lan_end'],
            'seat_reservation_start' => $this->paramsContent['seat_reservation_start'],
            'tournament_reservation_start' => $this->paramsContent['tournament_reservation_start'],
            'event_key_id' => $this->paramsContent['event_key_id'],
            'public_key_id' => $this->paramsContent['public_key_id'],
            'secret_key_id' => $this->paramsContent['secret_key_id'],
            'price' => $this->paramsContent['price'],
        ]);
    }

    public function testSignUpPriceUnsignedIntegerConstrain()
    {
        $this->paramsContent['price'] = -1;
        $this->lanRepository->createLan(
            new DateTime($this->paramsContent['lan_start']),
            new DateTime($this->paramsContent['lan_end']),
            new DateTime($this->paramsContent['seat_reservation_start']),
            new DateTime($this->paramsContent['tournament_reservation_start']),
            $this->paramsContent['event_key_id'],
            $this->paramsContent['public_key_id'],
            $this->paramsContent['secret_key_id'],
            $this->paramsContent['price']
        );
        $this->notSeeInDatabase('lan', [
            'lan_start' => $this->paramsContent['lan_start'],
            'lan_end' => $this->paramsContent['lan_end'],
            'seat_reservation_start' => $this->paramsContent['seat_reservation_start'],
            'tournament_reservation_start' => $this->paramsContent['tournament_reservation_start'],
            'event_key_id' => $this->paramsContent['event_key_id'],
            'public_key_id' => $this->paramsContent['public_key_id'],
            'secret_key_id' => $this->paramsContent['secret_key_id'],
            'price' => $this->paramsContent['price'],
        ]);
    }
}
