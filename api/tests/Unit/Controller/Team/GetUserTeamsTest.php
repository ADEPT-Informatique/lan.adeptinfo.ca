<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUserTeamsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

    protected $requestContent = [
        'lan_id' => null,
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

        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testCreateRequestNotConfirmed(): void
    {
        factory('App\Model\Request')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'               => 1,
                    'name'             => $this->team->name,
                    'player_state'     => 'not-confirmed',
                    'players_to_reach' => $this->tournament->players_to_reach,
                    'players_reached'  => 0,
                    'requests'         => 1,
                    'tag'              => $this->team->tag,
                    'tournament_name'  => $this->tournament->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testCreateRequestCurrentLan(): void
    {
        $this->requestContent['lan_id'] = null;
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $startTime = Carbon::parse($lan->lan_start);
        $endTime = Carbon::parse($lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id,
        ]);
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        factory('App\Model\Request')->create([
            'team_id' => $team->id,
            'tag_id'  => $tag->id,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'               => 2,
                    'name'             => $team->name,
                    'player_state'     => 'not-confirmed',
                    'players_to_reach' => $tournament->players_to_reach,
                    'players_reached'  => 0,
                    'requests'         => 1,
                    'tag'              => $team->tag,
                    'tournament_name'  => $tournament->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testCreateLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestConfirmed(): void
    {
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'               => 1,
                    'name'             => $this->team->name,
                    'player_state'     => 'confirmed',
                    'players_to_reach' => $this->tournament->players_to_reach,
                    'players_reached'  => 1,
                    'requests'         => 0,
                    'tag'              => $this->team->tag,
                    'tournament_name'  => $this->tournament->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testCreateRequestLeader(): void
    {
        factory('App\Model\TagTeam')->create([
            'team_id'   => $this->team->id,
            'tag_id'    => $this->tag->id,
            'is_leader' => true,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'               => 1,
                    'name'             => $this->team->name,
                    'player_state'     => 'leader',
                    'players_to_reach' => $this->tournament->players_to_reach,
                    'players_reached'  => 1,
                    'requests'         => 0,
                    'tag'              => $this->team->tag,
                    'tournament_name'  => $this->tournament->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testCreateRequestManyTeams(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id,
        ]);
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'team_id' => $team->id,
            'tag_id'  => $tag->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id'  => $this->tag->id,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'               => 1,
                    'name'             => $this->team->name,
                    'player_state'     => 'confirmed',
                    'players_to_reach' => $this->tournament->players_to_reach,
                    'players_reached'  => 1,
                    'requests'         => 0,
                    'tag'              => $this->team->tag,
                    'tournament_name'  => $this->tournament->name,
                ],
                [
                    'id'               => 2,
                    'name'             => $team->name,
                    'player_state'     => 'confirmed',
                    'players_to_reach' => $tournament->players_to_reach,
                    'players_reached'  => 1,
                    'requests'         => 0,
                    'tag'              => $team->tag,
                    'tournament_name'  => $tournament->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testCreateRequestNoTeam(): void
    {
        $this->team->delete();
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }

    public function testCreateRequestNoTournament(): void
    {
        $this->team->delete();
        $this->tournament->delete();
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }

    public function testCreateRequestNoTags(): void
    {
        $this->tag->delete();
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/user', $this->requestContent)
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }
}
