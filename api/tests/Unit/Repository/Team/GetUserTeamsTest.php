<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUserTeamsTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

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

    public function testCreateRequestNotConfirmed(): void
    {
        factory('App\Model\Request')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);

        $result = $this->teamRepository->getUserTeams($this->user->id, $this->lan->id);

        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals($this->team->name, $result[0]['name']);
        $this->assertEquals($this->team->tag, $result[0]['tag']);
    }
}
