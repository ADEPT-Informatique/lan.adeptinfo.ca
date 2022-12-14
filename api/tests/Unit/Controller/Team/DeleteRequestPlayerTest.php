<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteRequestPlayerTest extends TestCase
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
            'lan_id'           => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end'   => $endTime->subHour(1),
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

    public function testDeleteRequestPlayer(): void
    {
        $this->actingAs($this->requestingUser)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/player', $this->requestContent)
            ->seeJsonEquals([
                'id'            => $this->team->id,
                'name'          => $this->team->name,
                'tag'           => $this->team->tag,
                'tournament_id' => $this->team->tournament_id,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteRequestPlayerRequestIdInteger(): void
    {
        $this->requestContent['request_id'] = '???';
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/player', $this->requestContent)
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

    public function testDeleteRequestPlayerRequestIdExist(): void
    {
        $this->requestContent['request_id'] = -1;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/player', $this->requestContent)
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

    public function testDeleteRequestPlayerRequestBelongsToUser(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);
        $request = factory('App\Model\Request')->create([
            'tag_id'  => $tag,
            'team_id' => $team->id,
        ]);
        $this->requestContent['request_id'] = $request->id;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/request/player', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'request_id' => [
                        0 => 'The request must belong to the user.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
