<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminHasTournamentsTest extends TestCase
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
    }

    public function testAdminHasTournamentsTrue()
    {
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $this->tournament->id,
        ]);
        $result = $this->tournamentRepository->adminHasTournaments($this->user->id, $this->lan->id);
        $this->assertEquals($result, true);
    }

    public function testAdminHasTournamentsFalse()
    {
        $result = $this->tournamentRepository->adminHasTournaments($this->user->id, $this->lan->id);
        $this->assertEquals($result, false);
    }

    public function testAdminHasTournamentsFalseOtherLan()
    {
        $lan = factory('App\Model\Lan')->create();
        $startTime = Carbon::parse($lan->lan_start);
        $endTime = Carbon::parse($lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);
        $result = $this->tournamentRepository->adminHasTournaments($this->user->id, $this->lan->id);
        $this->assertEquals($result, false);
    }
}
