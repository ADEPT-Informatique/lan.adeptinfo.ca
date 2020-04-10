<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetOrganizerCountTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $organizer = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($lan->lan_start);
        $endTime = Carbon::parse($lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'teams_to_reach' => 10,
            'players_to_reach' => 10,
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);
        $organizer2 = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $organizer2->id,
            'tournament_id' => $this->tournament->id,
        ]);
    }

    public function testGetOrganizerCount(): void
    {
        $result = $this->tournamentRepository->getOrganizerCount($this->tournament->id);

        $this->assertEquals(2, $result);
    }
}
