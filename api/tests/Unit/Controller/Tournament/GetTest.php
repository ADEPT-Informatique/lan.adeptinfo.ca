<?php

namespace Tests\Unit\Controller\Tournament;

use App\Model\Reservation;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $user2;
    protected $user3;
    protected $tag;
    protected $tag2;
    protected $tag3;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $team2;
    protected $reservation;
    protected $reservation2;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->user2 = factory('App\Model\User')->create();
        $this->tag2 = factory('App\Model\Tag')->create([
            'user_id' => $this->user2->id,
        ]);
        $this->user3 = factory('App\Model\User')->create();
        $this->tag3 = factory('App\Model\Tag')->create([
            'user_id' => $this->user3->id,
        ]);

        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'teams_to_reach' => 10,
            'players_to_reach' => 10,
        ]);

        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag2->id,
            'team_id'   => $this->team->id,
            'is_leader' => false,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag3->id,
            'team_id'   => $this->team2->id,
            'is_leader' => true,
        ]);

        $this->reservation = new Reservation();
        $this->reservation->user_id = $this->user2->id;
        $this->reservation->lan_id = $this->lan->id;
        $this->reservation->seat_id = env('SEAT_TEST_ID');
        $this->reservation->save();

        $this->reservation2 = new Reservation();
        $this->reservation2->user_id = $this->user3->id;
        $this->reservation2->lan_id = $this->lan->id;
        $this->reservation2->seat_id = env('SEAT_TEST_ID_2');
        $this->reservation2->save();
    }

    public function testGet(): void
    {
        $this->json('GET', 'http://'.env('API_DOMAIN').'/tournament/details/'.$this->tournament->id)
            ->seeJsonEquals([
                'id'               => $this->tournament->id,
                'name'             => $this->tournament->name,
                'rules'            => $this->tournament->rules,
                'price'            => $this->tournament->price,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)),
                'tournament_end'   => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)),
                'teams_to_reach'   => $this->tournament->teams_to_reach,
                'teams_reached'    => 0,
                'players_to_reach' => $this->tournament->players_to_reach,
                'state'            => 'hidden',
                'teams'            => [
                    [
                        'id'              => $this->team->id,
                        'name'            => $this->team->name,
                        'tag'             => $this->team->tag,
                        'players_reached' => 2,
                        'players'         => [
                            [
                                'tag_id'         => $this->tag->id,
                                'tag_name'       => $this->tag->name,
                                'first_name'     => $this->user->first_name,
                                'last_name'      => $this->user->last_name,
                                'is_leader'      => true,
                                'reservation_id' => null,
                                'seat_id'        => null,
                            ],
                            [
                                'tag_id'         => $this->tag2->id,
                                'tag_name'       => $this->tag2->name,
                                'first_name'     => $this->user2->first_name,
                                'last_name'      => $this->user2->last_name,
                                'is_leader'      => false,
                                'reservation_id' => $this->reservation->id,
                                'seat_id'        => $this->reservation->seat_id,
                            ],
                        ],
                    ],
                    [
                        'id'              => $this->team2->id,
                        'name'            => $this->team2->name,
                        'tag'             => $this->team2->tag,
                        'players_reached' => 1,
                        'players'         => [
                            [
                                'tag_id'         => $this->tag3->id,
                                'tag_name'       => $this->tag3->name,
                                'first_name'     => $this->user3->first_name,
                                'last_name'      => $this->user3->last_name,
                                'is_leader'      => true,
                                'reservation_id' => $this->reservation2->id,
                                'seat_id'        => $this->reservation2->seat_id,
                            ],
                        ],
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetTournamentIdExist(): void
    {
        $this->json('GET', 'http://'.env('API_DOMAIN').'/tournament/details/'.-1)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The selected tournament id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
