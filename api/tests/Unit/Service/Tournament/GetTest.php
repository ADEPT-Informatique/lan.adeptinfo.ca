<?php

namespace Tests\Unit\Service\Tournament;

use App\Model\Reservation;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

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
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

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
        $result = $this->tournamentService->get($this->tournament->id)->jsonSerialize();

        $this->assertEquals($this->tournament->id, $result['id']);
        $this->assertEquals($this->tournament->name, $result['name']);
        $this->assertEquals($this->tournament->rules, $result['rules']);
        $this->assertEquals($this->tournament->price, $result['price']);
        $this->assertEquals($this->tournament->tournament_start, $result['tournament_start']);
        $this->assertEquals($this->tournament->tournament_end, $result['tournament_end']);
        $this->assertEquals($this->tournament->teams_to_reach, $result['teams_to_reach']);
        $this->assertEquals(0, $result['teams_reached']);
        $this->assertEquals($this->tournament->players_to_reach, $result['players_to_reach']);
        $this->assertEquals('hidden', $result['state']);

        $this->assertEquals($this->team->id, $result['teams']->jsonSerialize()[0]['id']);
        $this->assertEquals($this->team->name, $result['teams']->jsonSerialize()[0]['name']);
        $this->assertEquals($this->team->tag, $result['teams']->jsonSerialize()[0]['tag']);
        $this->assertEquals(2, $result['teams']->jsonSerialize()[0]['players_reached']);

        $this->assertEquals($this->tag->id, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['tag_id']);
        $this->assertEquals($this->tag->name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['tag_name']);
        $this->assertEquals($this->tag->name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['tag_name']);
        $this->assertEquals($this->user->first_name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['first_name']);
        $this->assertEquals($this->user->last_name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['last_name']);
        $this->assertEquals(true, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['is_leader']);
        $this->assertEquals(null, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['reservation_id']);
        $this->assertEquals(null, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[0]['seat_id']);

        $this->assertEquals($this->tag2->id, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['tag_id']);
        $this->assertEquals($this->tag2->name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['tag_name']);
        $this->assertEquals($this->tag2->name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['tag_name']);
        $this->assertEquals($this->user2->first_name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['first_name']);
        $this->assertEquals($this->user2->last_name, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['last_name']);
        $this->assertEquals(false, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['is_leader']);
        $this->assertEquals($this->reservation->id, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['reservation_id']);
        $this->assertEquals($this->reservation->seat_id, $result['teams']->jsonSerialize()[0]['players']->jsonSerialize()[1]['seat_id']);

        $this->assertEquals($this->team2->id, $result['teams']->jsonSerialize()[1]['id']);
        $this->assertEquals($this->team2->name, $result['teams']->jsonSerialize()[1]['name']);
        $this->assertEquals($this->team2->tag, $result['teams']->jsonSerialize()[1]['tag']);
        $this->assertEquals(1, $result['teams']->jsonSerialize()[1]['players_reached']);

        $this->assertEquals($this->tag3->id, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['tag_id']);
        $this->assertEquals($this->tag3->name, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['tag_name']);
        $this->assertEquals($this->tag3->name, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['tag_name']);
        $this->assertEquals($this->user3->first_name, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['first_name']);
        $this->assertEquals($this->user3->last_name, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['last_name']);
        $this->assertEquals(true, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['is_leader']);
        $this->assertEquals($this->reservation2->id, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['reservation_id']);
        $this->assertEquals($this->reservation2->seat_id, $result['teams']->jsonSerialize()[1]['players']->jsonSerialize()[0]['seat_id']);
    }
}
