<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteRequestLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $leader;
    protected $requestingUser;
    protected $leadersTag;
    protected $requestingUsersTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $request;

    protected $requestContent = [
        'request_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->leader = factory('App\Model\User')->create();
        $this->leadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
        ]);
        $this->requestingUser = factory('App\Model\User')->create();
        $this->requestingUsersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->requestingUser->id,
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

        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->leadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);
        $this->request = factory('App\Model\Request')->create([
            'tag_id'  => $this->requestingUsersTag->id,
            'team_id' => $this->team->id,
        ]);

        $this->requestContent['request_id'] = $this->request->id;
    }

    public function testDeleteRequestLeader(): void
    {
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/leader', $this->requestContent)
            ->seeJsonEquals([
                'id'   => $this->requestingUsersTag->id,
                'name' => $this->requestingUsersTag->name,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteRequestLeaderRequestIdInteger(): void
    {
        $this->requestContent['request_id'] = '☭';
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'request_id' => [
                        0 => 'The request id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteRequestLeaderRequestIdExist(): void
    {
        $this->requestContent['request_id'] = -1;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'request_id' => [
                        0 => 'The selected request id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteRequestLeaderTeamIdUserIsTeamLeader(): void
    {
        $this->actingAs($this->requestingUser)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}
