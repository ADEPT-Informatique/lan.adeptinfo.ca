<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $leader;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

    protected $requestContent = [
        'team_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
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
        $this->leader = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $this->requestContent['team_id'] = $this->team->id;
    }

    public function testDeleteLeader(): void
    {
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'id'            => $this->team->id,
                'name'          => $this->team->name,
                'tag'           => $this->team->tag,
                'tournament_id' => $this->team->tournament_id,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteLeaderTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The team id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteLeaderTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The selected team id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteLeaderTeamIdUserIsTeamLeader(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}
