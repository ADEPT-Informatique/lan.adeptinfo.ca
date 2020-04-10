<?php

namespace Tests\Unit\Service\Tournament;

use App\Model\TagTeam;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAllTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $user;
    protected $tag;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->actingAs($this->user);
    }

    public function testGetAllHidden(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('hidden', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllFinished(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'state' => 'finished',
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('finished', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllFourthcoming(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'lan_end'   => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'state' => 'visible',
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('fourthcoming', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllLate(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(-1)->format('Y-m-d H:i:s'),
            'lan_end'   => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'state' => 'visible',
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('late', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllOutguessed(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'lan_end'   => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'state' => 'started',
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('outguessed', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllBehindhand(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(-1)->format('Y-m-d H:i:s'),
            'lan_end'   => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $startTime->addHour(2),
            'state' => 'started',
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('behindhand', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllRunning(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'lan_start' => Carbon::now()->addDays(-1)->format('Y-m-d H:i:s'),
            'lan_end'   => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'state' => 'started',
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('running', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllTeamsReachedTeamFull(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $users = factory('App\Model\User', $tournament->players_to_reach)->create();
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id,
        ]);
        foreach ($users as $user) {
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            $tagTeam = new TagTeam();
            $tagTeam->tag_id = $tag->id;
            $tagTeam->team_id = $team->id;
            $tagTeam->save();
        }

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('hidden', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllTeamsReachedTeamEmpty(): void
    {
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $users = factory('App\Model\User', $tournament->players_to_reach - 1)->create();
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id,
        ]);
        foreach ($users as $user) {
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            $tagTeam = new TagTeam();
            $tagTeam->tag_id = $tag->id;
            $tagTeam->team_id = $team->id;
            $tagTeam->save();
        }

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('hidden', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }

    public function testGetAllCurrentLan(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->user->id,
            'tournament_id' => $tournament->id,
        ]);

        $result = $this->tournamentService->getAll($this->lan->id);

        $this->assertEquals($tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_start)), $result[0]->jsonSerialize()['tournament_start']);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($tournament->tournament_end)), $result[0]->jsonSerialize()['tournament_end']);
        $this->assertEquals('hidden', $result[0]->jsonSerialize()['state']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['teams_reached']);
        $this->assertEquals($tournament->teams_to_reach, $result[0]->jsonSerialize()['teams_to_reach']);
    }
}
