<?php

namespace Tests\Service;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class SeatServiceImplTest extends SeatsTestCase
{
    protected $seatService;

    use DatabaseMigrations;

    protected $paramsContent = [
        'seat_id' => "A-1"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');
    }

    public function testBookSeat()
    {
        $this->be(factory('App\Model\User')->create());
        $lan = factory('App\Model\Lan')->create();

        $result = $this->seatService->book($lan->id, $this->paramsContent['seat_id']);

        $this->assertEquals($this->paramsContent['seat_id'], $result->seat_id);
        $this->assertEquals($lan->id, $result->lan_id);
    }

    public function testBookLanIdExistConstraint()
    {
        $this->be(factory('App\Model\User')->create());
        $badLanId = -1;

        try {
            $this->seatService->book($badLanId, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}', $e->getMessage());
        }
    }

    public function testBookSeatIdExistConstraint()
    {
        $this->be(factory('App\Model\User')->create());
        $lan = factory('App\Model\Lan')->create();
        $badSeatId = '-1';

        try {
            $this->seatService->book($lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["Seat with id ' . $badSeatId . ' doesn\'t exist in this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["Seat with id ' . $badSeatId . ' doesn\'t exist in this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatAvailableConstraint()
    {
        $this->be(factory('App\Model\User')->create());
        $lan = factory('App\Model\Lan')->create();

        $seatsClient = new SeatsioClient($lan->secret_key_id);
        $seatsClient->events()->book($lan->event_key_id, [$this->paramsContent['seat_id']]);

        try {
            $this->seatService->book($lan->id, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatUniqueUserInLanConstraint()
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $lan = factory('App\Model\Lan')->create();

        $lan->user()->attach($user->id, [
            "seat_id" => $this->paramsContent['seat_id']
        ]);

        try {
            $this->seatService->book($lan->id, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"lan_id":["The user already has a seat at this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The user already has a seat at this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatOnceInLanConstraint()
    {
        $this->be(factory('App\Model\User')->create());
        $lan = factory('App\Model\Lan')->create();

        $otherUser = factory('App\Model\User')->create();

        $lan->user()->attach($otherUser->id, [
            "seat_id" => $this->paramsContent['seat_id']
        ]);

        try {
            $this->seatService->book($lan->id, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatLanIdInteger()
    {
        $this->be(factory('App\Model\User')->create());
        $badLanId = '☭';

        try {
            $this->seatService->book($badLanId, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}
