<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ChangeLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $leader;
    protected $toBeLeader;
    protected $leadersTag;
    protected $toBeLeadersTag;
    protected $lan;
    protected $tournament;
    protected $team;

    protected $requestContent = [
        'team_id' => null,
        'tag_id'  => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->leader = factory('App\Model\User')->create();
        $this->leadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
        ]);
        $this->toBeLeader = factory('App\Model\User')->create();
        $this->toBeLeadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->toBeLeader->id,
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
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->toBeLeadersTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => false,
        ]);

        $this->requestContent['team_id'] = $this->team->id;
        $this->requestContent['tag_id'] = $this->toBeLeadersTag->id;
    }

    public function testChangeLeader(): void
    {
        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'id'   => $this->toBeLeadersTag->id,
                'name' => $this->toBeLeadersTag->name,
            ])
            ->assertResponseStatus(200);
    }

    public function testChangeLeaderTagIdInteger(): void
    {
        $this->requestContent['tag_id'] = '☭';
        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testChangeLeaderTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The selected tag id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testChangeLeaderTagIdTagBelongsInTeam(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $this->requestContent['tag_id'] = $tag->id;

        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag must be in the team.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testChangeLeaderTagIdTagNotBelongsLeader(): void
    {
        $this->requestContent['tag_id'] = $this->leadersTag->id;
        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag must not belong to the leader of the team.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testChangeLeaderTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
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

    public function testChangeLeaderTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->leader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
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

    public function testChangeLeaderTeamIdUserIsTeamLeader(): void
    {
        $this->actingAs($this->toBeLeader)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/team/leader', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}
