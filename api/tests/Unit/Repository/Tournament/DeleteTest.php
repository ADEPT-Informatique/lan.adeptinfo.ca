<?php

namespace Tests\Unit\Repository\Tournament;

use App\Model\Team;
use App\Model\Tournament;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $tagTeam;
    protected $request;
    protected $organizer;
    protected $organizerTournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

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

        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id,
        ]);

        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        $this->tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $this->request = factory('App\Model\Request')->create([
            'tag_id'  => $tag2->id,
            'team_id' => $this->team->id,
        ]);

        $this->organizer = factory('App\Model\User')->create();
        $this->organizerTournament = factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);
    }

    public function testDelete(): void
    {
        $this->seeInDatabase('tag_team', [
            'id'        => $this->tagTeam->id,
            'tag_id'    => $this->tagTeam->tag_id,
            'team_id'   => $this->tagTeam->team_id,
            'is_leader' => $this->tagTeam->is_leader,
        ]);
        $this->seeInDatabase('request', [
            'id'      => $this->request->id,
            'tag_id'  => $this->request->tag_id,
            'team_id' => $this->request->team_id,
        ]);
        $this->seeInDatabase('organizer_tournament', [
            'id'            => $this->organizerTournament->id,
            'organizer_id'  => $this->organizerTournament->organizer_id,
            'tournament_id' => $this->organizerTournament->tournament_id,
        ]);

        $this->tournamentRepository->delete($this->tournament->id);

        $tournament = Tournament::withTrashed()->first();
        $team = Team::withTrashed()->first();

        $this->assertEquals($this->tournament->id, $tournament->id);
        $this->assertEquals($this->team->id, $team->id);
        $this->notSeeInDatabase('tag_team', [
            'id'        => $this->tagTeam->id,
            'tag_id'    => $this->tagTeam->tag_id,
            'team_id'   => $this->tagTeam->team_id,
            'is_leader' => $this->tagTeam->is_leader,
        ]);
        $this->notSeeInDatabase('request', [
            'id'      => $this->request->id,
            'tag_id'  => $this->request->tag_id,
            'team_id' => $this->request->team_id,
        ]);
        $this->notSeeInDatabase('organizer_tournament', [
            'id'            => $this->organizerTournament->id,
            'organizer_id'  => $this->organizerTournament->organizer_id,
            'tournament_id' => $this->organizerTournament->tournament_id,
        ]);
    }
}
