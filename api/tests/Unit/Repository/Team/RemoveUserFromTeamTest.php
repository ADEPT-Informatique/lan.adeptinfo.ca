<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RemoveUserFromTeamTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

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
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

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

    public function testRemoveUserFromTeam(): void
    {
        $this->seeInDatabase('tag_team', [
            'tag_id'  => $this->userTag->id,
            'team_id' => $this->team->id,
        ]);
        $this->seeInDatabase('tag_team', [
            'tag_id'  => $this->leaderTag->id,
            'team_id' => $this->team->id,
        ]);

        $this->teamRepository->removeUserFromTeam($this->user->id, $this->team->id);

        $this->notSeeInDatabase('tag_team', [
            'tag_id'  => $this->userTag->id,
            'team_id' => $this->team->id,
        ]);
        $this->seeInDatabase('tag_team', [
            'tag_id'  => $this->leaderTag->id,
            'team_id' => $this->team->id,
        ]);
    }
}
