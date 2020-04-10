<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DissociateOrganizerTournamentTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $lan;
    protected $tournament;
    protected $organizer;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'teams_to_reach' => 10,
            'players_to_reach' => 10,
        ]);
        $this->organizer = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);
    }

    public function testDissociateOrganizerTournament(): void
    {
        $this->seeInDatabase('organizer_tournament', [
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $this->tournamentRepository->dissociateOrganizerTournament(
            $this->organizer->id,
            $this->tournament->id
        );

        $this->notSeeInDatabase('organizer_tournament', [
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);
    }
}
