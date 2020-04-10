<?php

namespace Tests\Unit\Controller\Tournament;

use App\Model\Permission;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'tournament_id'    => null,
        'name'             => 'October',
        'state'            => 'visible',
        'tournament_start' => null,
        'tournament_end'   => null,
        'players_to_reach' => 5,
        'teams_to_reach'   => 6,
        'rules'            => 'The Bolsheviks seize control of Petrograd.',
        'price'            => 0,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(0)->format('Y-m-d H:i:s');
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour()->format('Y-m-d H:i:s');
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
        $permission = Permission::where('name', 'edit-tournament')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id'       => $role->id,
            'permission_id' => $permission->id,
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function testUpdate(): void
    {
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'id'               => 1,
                'name'             => $this->requestContent['name'],
                'state'            => 'fourthcoming',
                'tournament_start' => $this->requestContent['tournament_start'],
                'tournament_end'   => $this->requestContent['tournament_end'],
                'players_to_reach' => $this->requestContent['players_to_reach'],
                'teams_to_reach'   => $this->requestContent['teams_to_reach'],
                'teams_reached'    => 0,
                'teams'            => [],
                'rules'            => $this->requestContent['rules'],
                'price'            => $this->requestContent['price'],
            ])
            ->assertResponseStatus(200);
    }

    public function testUpdateHasPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testUpdateTournamentIdExist(): void
    {
        $badTournamentId = -1;
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$badTournamentId, $this->requestContent)
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

    public function testUpdateNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'The name must be a string.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'The name may not be greater than 255 characters.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateStateInEnum(): void
    {
        $this->requestContent['state'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'state' => [
                        0 => 'The selected state is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdatePriceInteger(): void
    {
        $this->requestContent['price'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'price' => [
                        0 => 'The price must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdatePriceMin(): void
    {
        $this->requestContent['price'] = -1;
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'price' => [
                        0 => 'The price must be at least 0.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateTournamentStartAfterOrEqualLanStartTime(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->subHour()->format('Y-m-d H:i:s');
        $this->actingAs($this->user)
            ->json('PUT', 'http://' . env('API_DOMAIN') . '/tournament/' . $this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tournament_start' => [
                        0 => 'The tournament start time must be after or equal the lan start time.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateTournamentEndBeforeOrEqualLanEndTime(): void
    {
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->addHour(0)->format('Y-m-d H:i:s');
        $this->actingAs($this->user)
            ->json('PUT', 'http://' . env('API_DOMAIN') . '/tournament/' . $this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tournament_end' => [
                        0 => 'The tournament end time must be before or equal the lan end time.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdatePlayersToReachMin(): void
    {
        $this->requestContent['players_to_reach'] = 0;
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'players_to_reach' => [
                        0 => 'The players to reach must be at least 1.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdatePlayersToReachInteger(): void
    {
        $this->requestContent['players_to_reach'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'players_to_reach' => [
                        0 => 'The players to reach must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdatePlayersToReachLock(): void
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
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The players to reach can\'t be changed once users have started registering for the tournament.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateTeamsToReachMin(): void
    {
        $this->requestContent['teams_to_reach'] = 0;
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'teams_to_reach' => [
                        0 => 'The teams to reach must be at least 1.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateTeamsToReachInteger(): void
    {
        $this->requestContent['teams_to_reach'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'teams_to_reach' => [
                        0 => 'The teams to reach must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateRulesString(): void
    {
        $this->requestContent['rules'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id, $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'rules' => [
                        0 => 'The rules must be a string.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
