<?php

namespace Tests\Unit\Service\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RemoveOrganizerTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $admin;
    protected $organizer;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->admin = factory('App\Model\User')->create();
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

    public function testRemoveOrganizer(): void
    {
        $organizer2 = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $organizer2->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $result = $this->tournamentService->removeOrganizer($this->organizer->email, $this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
        $this->assertEquals($this->tournament->price, $result->price);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)), $result->tournament_start);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)), $result->tournament_end);
        $this->assertEquals($this->tournament->players_to_reach, $result->players_to_reach);
        $this->assertEquals($this->tournament->teams_to_reach, $result->teams_to_reach);
        $this->assertEquals($this->tournament->rules, $result->rules);
    }

    public function testRemoveOrganizerLastOrganizer(): void
    {
        $result = $this->tournamentService->removeOrganizer($this->organizer->email, $this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
        $this->assertEquals($this->tournament->price, $result->price);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)), $result->tournament_start);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)), $result->tournament_end);
        $this->assertEquals($this->tournament->players_to_reach, $result->players_to_reach);
        $this->assertEquals($this->tournament->teams_to_reach, $result->teams_to_reach);
        $this->assertEquals($this->tournament->rules, $result->rules);
    }
}
