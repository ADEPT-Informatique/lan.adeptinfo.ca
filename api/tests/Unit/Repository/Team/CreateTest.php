<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'name' => 'WorkersUnite',
        'tag'  => 'PRO',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

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
        $this->notSeeInDatabase('team', [
            'id'            => 1,
            'name'          => $this->requestContent['name'],
            'tag'           => $this->requestContent['tag'],
            'tournament_id' => $this->tournament->id,
        ]);

        $result = $this->teamRepository->create(
            $this->tournament->id,
            $this->requestContent['name'],
            $this->requestContent['tag']
        );

        $this->assertEquals(1, $result);
        $this->seeInDatabase('team', [
            'id'            => 1,
            'name'          => $this->requestContent['name'],
            'tag'           => $this->requestContent['tag'],
            'tournament_id' => $this->tournament->id,
        ]);
    }
}
