<?php

namespace Tests\Unit\Controller\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class UnAssignTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $admin;
    protected $lan;

    protected $requestContent = [
        'lan_id'  => null,
        'seat_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->admin->id,
            $this->lan->id,
            'unassign-seat'
        );
    }

    public function testUnAssign(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $this->lan->id,
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.env('SEAT_TEST_ID'), [
                'lan_id'     => $this->lan->id,
                'user_email' => $this->user->email,
            ])
            ->seeJsonEquals([
                'seat_id' => env('SEAT_TEST_ID'),
            ])
            ->assertResponseStatus(200);
    }

    public function testUnAssignCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);

        $this->addLanPermissionToUser(
            $this->admin->id,
            $lan->id,
            'unassign-seat'
        );

        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $lan->id,
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.env('SEAT_TEST_ID'), [
                'user_email' => $this->user->email,
            ])
            ->seeJsonEquals([
                'seat_id' => env('SEAT_TEST_ID'),
            ])
            ->assertResponseStatus(200);
    }

    public function testUnAssignHasPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.env('SEAT_TEST_ID'), [
                'lan_id'     => $this->lan->id,
                'user_email' => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testUnAssignLanIdExist()
    {
        $badLanId = -1;
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $this->lan->id,
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.env('SEAT_TEST_ID'), [
                'lan_id'     => $badLanId,
                'user_email' => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUnAssignIdExist()
    {
        $badSeatId = '???';
        $this->actingAs($this->admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.$badSeatId, [
                'lan_id'     => $this->lan->id,
                'user_email' => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The selected seat id is invalid.',
                        1 => 'The relation between seat with id '.$badSeatId.' and LAN with id 1 doesn\'t exist.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUnAssignLanIdInteger()
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $this->lan->id,
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.env('SEAT_TEST_ID'), [
                'lan_id'     => '???',
                'user_email' => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUnAssignEmailExists()
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $this->lan->id,
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/seat/assign/'.env('SEAT_TEST_ID'), [
                'lan_id'     => $this->lan->id,
                'user_email' => '???',
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'user_email' => [
                        0 => 'The selected user email is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
