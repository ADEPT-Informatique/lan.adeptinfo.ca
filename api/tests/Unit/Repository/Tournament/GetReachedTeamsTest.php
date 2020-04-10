<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetReachedTeamsTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $user2;
    protected $user3;
    protected $tag;
    protected $tag2;
    protected $tag3;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $team2;
    protected $reservation;
    protected $reservation2;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->user2 = factory('App\Model\User')->create();
        $this->tag2 = factory('App\Model\Tag')->create([
            'user_id' => $this->user2->id,
        ]);
        $this->user3 = factory('App\Model\User')->create();
        $this->tag3 = factory('App\Model\Tag')->create([
            'user_id' => $this->user3->id,
        ]);

        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'teams_to_reach' => 1,
            'players_to_reach' => 2,
        ]);

        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag2->id,
            'team_id'   => $this->team->id,
            'is_leader' => false,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag3->id,
            'team_id'   => $this->team2->id,
            'is_leader' => true,
        ]);
    }

    public function testGetReachedTeams(): void
    {
        $result = $this->tournamentRepository->getReachedTeams($this->tournament->id);

        $this->assertEquals(1, $result);
    }
}
