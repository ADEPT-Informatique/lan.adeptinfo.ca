<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $leader;
    protected $userTag;
    protected $leaderTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $userTagTeam;

    protected $requestContent = [
        'team_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->userTag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->leader = factory('App\Model\User')->create();
        $this->leaderTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
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

        $this->userTagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'  => $this->userTag->id,
            'team_id' => $this->team->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->leaderTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $this->requestContent['team_id'] = $this->team->id;
    }

    public function testLeave(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/leave', $this->requestContent)
            ->seeJsonEquals([
                'id'            => $this->team->id,
                'name'          => $this->team->name,
                'tag'           => $this->team->tag,
                'tournament_id' => $this->team->tournament_id,
            ])
            ->assertResponseStatus(200);
    }

    public function testLeaveIsLeader(): void
    {
        $this->actingAs($this->leader)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/leave', $this->requestContent)
            ->seeJsonEquals([
                'id'            => $this->team->id,
                'name'          => $this->team->name,
                'tag'           => $this->team->tag,
                'tournament_id' => $this->team->tournament_id,
            ])
            ->assertResponseStatus(200);
    }

    public function testLeaveLeaderLastPlayer(): void
    {
        $this->userTagTeam->delete();
        $this->userTag->delete();
        $this->user->delete();
        $this->actingAs($this->leader)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/leave', $this->requestContent)
            ->seeJsonEquals([
                'id'            => $this->team->id,
                'name'          => $this->team->name,
                'tag'           => $this->team->tag,
                'tournament_id' => $this->team->tournament_id,
            ])
            ->assertResponseStatus(200);
    }

    public function testLeaveTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/leave', $this->requestContent)
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

    public function testLeaveTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/leave', $this->requestContent)
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
}
