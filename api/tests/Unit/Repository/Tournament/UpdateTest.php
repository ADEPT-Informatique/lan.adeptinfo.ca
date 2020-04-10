<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'name'             => 'October',
        'state'            => 'visible',
        'tournament_start' => null,
        'tournament_end'   => null,
        'players_to_reach' => 5,
        'teams_to_reach'   => 6,
        'rules'            => 'The Bolsheviks seize control of Petrograd.',
        'price'            => 0,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $startTime = Carbon::parse($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(0);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour();
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
    }

    public function testUpdate(): void
    {
        $this->tournamentRepository->update(
            $this->tournament->id,
            $this->requestContent['name'],
            $this->requestContent['state'],
            Carbon::parse($this->requestContent['tournament_start']),
            Carbon::parse($this->requestContent['tournament_end']),
            $this->requestContent['players_to_reach'],
            $this->requestContent['teams_to_reach'],
            $this->requestContent['rules'],
            $this->requestContent['price']
        );

        $this->seeInDatabase('tournament', [
            'id'               => 1,
            'name'             => $this->requestContent['name'],
            'state'            => $this->requestContent['state'],
            'tournament_start' => $this->requestContent['tournament_start'],
            'tournament_end'   => $this->requestContent['tournament_end'],
            'players_to_reach' => $this->requestContent['players_to_reach'],
            'teams_to_reach'   => $this->requestContent['teams_to_reach'],
            'rules'            => $this->requestContent['rules'],
            'price'            => $this->requestContent['price'],
        ]);
    }
}
