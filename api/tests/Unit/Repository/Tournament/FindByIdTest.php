<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);

        $this->be($this->user);
    }

    public function testFindById(): void
    {
        $result = $this->tournamentRepository->findById($this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
    }
}
