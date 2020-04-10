<?php

namespace Tests\Unit\Controller\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $organizer;
    protected $organizerTournament;

    public function setUp(): void
    {
        parent::setUp();

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

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'delete-tournament'
        );
    }

    public function testDeleteHasPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testDelete(): void
    {
        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id,
        ]);

        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $team->id,
            'is_leader' => true,
        ]);

        factory('App\Model\Request')->create([
            'tag_id'  => $tag2->id,
            'team_id' => $team->id,
        ]);

        $this->organizer = factory('App\Model\User')->create();
        $this->organizerTournament = factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id)
            ->seeJsonEquals([
                'id'               => $this->tournament->id,
                'name'             => $this->tournament->name,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)),
                'tournament_end'   => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)),
                'teams_to_reach'   => $this->tournament->teams_to_reach,
                'teams_reached'    => 0,
                'state'            => 'hidden',
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteTournamentIdExit(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.-1)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The selected tournament id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
