<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'name' => 'WorkersUnite',
        'tag'  => 'PRO',
    ];

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
    }

    public function testCreate(): void
    {
        $result = $this->teamService->create(
            $this->tournament->id,
            $this->requestContent['name'],
            $this->requestContent['tag'],
            $this->tag->id
        )->jsonSerialize();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->requestContent['tag'], $result['tag']);
        $this->assertEquals($this->requestContent['name'], $result['name']);
        $this->assertEquals($this->tournament->id, $result['tournament_id']);
    }

    public function testCreateUserTagIdUniqueUserPerTournamentSameLan(): void
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
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $team->id,
            'is_leader' => true,
        ]);

        $result = $this->teamService->create(
            $tournament->id,
            $this->requestContent['name'],
            $this->requestContent['tag'],
            $this->tag->id
        )->jsonSerialize();

        $this->assertEquals(2, $result['id']);
        $this->assertEquals($this->requestContent['tag'], $result['tag']);
        $this->assertEquals($this->requestContent['name'], $result['name']);
        $this->assertEquals($tournament->id, $result['tournament_id']);
    }
}
