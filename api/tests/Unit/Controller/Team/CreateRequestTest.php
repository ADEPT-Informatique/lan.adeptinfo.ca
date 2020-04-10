<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
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

        $this->requestContent['team_id'] = $this->team->id;
        $this->requestContent['tag_id'] = $this->tag->id;
    }

    public function testCreateRequest(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'id'      => 1,
                'team_id' => $this->requestContent['team_id'],
                'tag_id'  => $this->requestContent['tag_id'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateRequestTeamIdRequired(): void
    {
        $this->requestContent['team_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The team id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
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

    public function testCreateRequestUserUniqueUserPerRequest(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent);
        $this->requestContent['tag_id'] = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ])->id;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'A user can only have one request per team.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestUserTagIdRequired(): void
    {
        $this->requestContent['tag_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestUserTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
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

    public function testCreateRequestUserTeamIdUniqueUserPerTournamentSameUser(): void
    {
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'  => $tag->id,
            'team_id' => $team->id,
        ]);

        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'A user can only be once in a tournament.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestUserTagIdUniqueUserPerTournamentSameLan(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', [
                'tournament_id' => $tournament->id,
                'user_tag_id'   => $this->tag->id,
                'name'          => 'name',
                'tag'           => 'tag',
            ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'id'      => 1,
                'team_id' => $this->requestContent['team_id'],
                'tag_id'  => $this->requestContent['tag_id'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateRequestTagBelongsToUser(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $this->requestContent['tag_id'] = $tag->id;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}
