<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUserTeamsTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

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
        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        $this->be($this->user);
    }

    public function testGetUserTeamsNotConfirmed(): void
    {
        factory('App\Model\Request')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);

        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('not-confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testGetUserTeamsConfirmed(): void
    {
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);

        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testGetUserTeamsLeader(): void
    {
        factory('App\Model\TagTeam')->create([
            'team_id'   => $this->team->id,
            'tag_id'    => $this->tag->id,
            'is_leader' => true,
        ]);

        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('leader', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testGetUserTeamsManyTeams(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id,
        ]);
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'team_id' => $team->id,
            'tag_id'  => $tag->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);

        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);

        $this->assertEquals(2, $result[1]->jsonSerialize()['id']);
        $this->assertEquals($team->name, $result[1]->jsonSerialize()['name']);
        $this->assertEquals('confirmed', $result[1]->jsonSerialize()['player_state']);
        $this->assertEquals($tournament->players_to_reach, $result[1]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[1]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[1]->jsonSerialize()['requests']);
        $this->assertEquals($team->tag, $result[1]->jsonSerialize()['tag']);
        $this->assertEquals($tournament->name, $result[1]->jsonSerialize()['tournament_name']);
    }

    public function testGetUserTeamsNoTeam(): void
    {
        $this->team->delete();
        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals([], $result->jsonSerialize());
    }

    public function testGetUserTeamsNoTournament(): void
    {
        $this->team->delete();
        $this->tournament->delete();
        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals([], $result->jsonSerialize());
    }

    public function testGetUserTeamsNoTags(): void
    {
        $this->tag->delete();
        $result = $this->teamService->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals([], $result->jsonSerialize());
    }
}
