<?php

namespace Tests\Unit\Controller\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class QuitTest extends TestCase
{
    use DatabaseMigrations;

    protected $organizer;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();

        $this->organizer = factory('App\Model\User')->create();
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
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);
    }

    public function testQuit(): void
    {
        $organizer2 = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $organizer2->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/quit')
            ->seeJsonEquals([
                'id'               => $this->tournament->id,
                'name'             => $this->tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)),
                'tournament_end'   => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)),
                'teams_to_reach'   => $this->tournament->teams_to_reach,
                'teams_reached'    => 0,
                'state'            => 'hidden',
            ])
            ->assertResponseStatus(200);
    }

    public function testQuitLastOrganizer(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/quit')
            ->seeJsonEquals([
                'id'               => $this->tournament->id,
                'name'             => $this->tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)),
                'tournament_end'   => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)),
                'teams_to_reach'   => $this->tournament->teams_to_reach,
                'teams_reached'    => 0,
                'state'            => 'hidden',
            ])
            ->assertResponseStatus(200);
    }

    public function testQuitTournamentIdExist(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.-1 .'/quit')
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

    public function testQuitUserIsTournamentAdmin(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/quit')
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The user doesn\'t have any tournaments',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
