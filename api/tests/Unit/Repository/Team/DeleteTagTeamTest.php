<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTagTeamTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $tagTeam;

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

        $this->tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'  => $this->tag->id,
            'team_id' => $this->team->id,
        ]);
    }

    public function testDeleteTagTeam(): void
    {
        $this->seeInDatabase('tag_team', [
            'id'      => $this->tagTeam->id,
            'tag_id'  => $this->tagTeam->tag_id,
            'team_id' => $this->tagTeam->team_id,
        ]);

        $this->teamRepository->deleteTagTeam($this->tag->id, $this->team->id);

        $this->notSeeInDatabase('tag_team', [
            'id'      => $this->tagTeam->id,
            'tag_id'  => $this->tagTeam->tag_id,
            'team_id' => $this->tagTeam->team_id,
        ]);
    }
}
