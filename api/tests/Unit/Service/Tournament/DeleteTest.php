<?php

namespace Tests\Unit\Service\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $organizer;
    protected $organizerTournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);

        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
    }

    public function testDelete(): void
    {
        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id,
        ]);

        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $team->id,
            'is_leader' => true,
        ]);

        factory('App\Model\Request')->create([
            'tag_id'  => $tag2->id,
            'team_id' => $team->id,
        ]);

        $this->organizer = factory('App\Model\User')->create();
        $this->organizerTournament = factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $result = $this->tournamentService->delete($this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
        $this->assertEquals($this->tournament->price, $result->price);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)), $result->tournament_start);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)), $result->tournament_end);
        $this->assertEquals($this->tournament->players_to_reach, $result->players_to_reach);
        $this->assertEquals($this->tournament->teams_to_reach, $result->teams_to_reach);
        $this->assertEquals('hidden', $result->state);
        $this->assertEquals($this->tournament->rules, $result->rules);
    }
}
