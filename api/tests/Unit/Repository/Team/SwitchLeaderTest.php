<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SwitchLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $leader;
    protected $toBeLeader;
    protected $leadersTag;
    protected $toBeLeadersTag;
    protected $lan;
    protected $tournament;
    protected $team;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

        $this->leader = factory('App\Model\User')->create();
        $this->leadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
        ]);
        $this->toBeLeader = factory('App\Model\User')->create();
        $this->toBeLeadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->toBeLeader->id,
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

        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->leadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->toBeLeadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => false,
        ]);
    }

    public function testChangeLeader(): void
    {
        $this->seeInDatabase('tag_team', [
            'tag_id'    => $this->toBeLeadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => false,
        ]);
        $this->seeInDatabase('tag_team', [
            'tag_id'    => $this->leadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $this->teamRepository->switchLeader($this->toBeLeadersTag->id, $this->team->id);

        $this->seeInDatabase('tag_team', [
            'tag_id'    => $this->toBeLeadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);
        $this->seeInDatabase('tag_team', [
            'tag_id'    => $this->leadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => false,
        ]);
    }
}
