<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteRequestPlayerTest extends TestCase
{
    use DatabaseMigrations;
    protected $teamService;
    protected $leader;
    protected $requestingUser;
    protected $leadersTag;
    protected $requestingUsersTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');
        $this->leader = factory('App\Model\User')->create();
        $this->leadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
        ]);
        $this->requestingUser = factory('App\Model\User')->create();
        $this->requestingUsersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->requestingUser->id,
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
        $this->request = factory('App\Model\Request')->create([
            'tag_id'  => $this->requestingUsersTag->id,
            'team_id' => $this->team->id,
        ]);
    }

    public function testDeleteRequestPlayer(): void
    {
        $result = $this->teamService->deleteRequestPlayer($this->request->id);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }
}
