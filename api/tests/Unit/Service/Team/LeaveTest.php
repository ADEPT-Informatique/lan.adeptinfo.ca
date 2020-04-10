<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $leader;
    protected $userTag;
    protected $leaderTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $userTagTeam;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->userTag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->leader = factory('App\Model\User')->create();
        $this->leaderTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
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

        $this->userTagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'  => $this->userTag->id,
            'team_id' => $this->team->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->leaderTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);
    }

    public function testLeave(): void
    {
        $result = $this->teamService->leave($this->user->id, $this->team->id);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }

    public function testLeaveIsLeader(): void
    {
        $result = $this->teamService->leave($this->leader->id, $this->team->id);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }

    public function testLeaveLeaderLastPlayer(): void
    {
        $this->userTagTeam->delete();
        $this->userTag->delete();
        $this->user->delete();
        $result = $this->teamService->leave($this->leader->id, $this->team->id);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }
}
