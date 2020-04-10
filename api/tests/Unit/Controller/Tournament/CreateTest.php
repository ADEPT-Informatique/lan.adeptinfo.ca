<?php

namespace Tests\Unit\Controller\Tournament;

use App\Model\Permission;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'lan_id'           => null,
        'name'             => 'October',
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

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
        $permission = Permission::where('name', 'create-tournament')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id'       => $role->id,
            'permission_id' => $permission->id,
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function testCreate(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'id'               => 1,
                'name'             => $this->requestContent['name'],
                'tournament_start' => $this->requestContent['tournament_start'],
                'tournament_end'   => $this->requestContent['tournament_end'],
                'players_to_reach' => $this->requestContent['players_to_reach'],
                'teams_to_reach'   => $this->requestContent['teams_to_reach'],
                'teams_reached'    => 0,
                'rules'            => $this->requestContent['rules'],
                'price'            => $this->requestContent['price'],
                'teams'            => [],
                'state'            => 'hidden',
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id,
        ]);
        $permission = Permission::where('name', 'create-tournament')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id'       => $role->id,
            'permission_id' => $permission->id,
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id,
        ]);
        $startTime = Carbon::parse($lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(0)->format('Y-m-d H:i:s');
        $endTime = Carbon::parse($lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour()->format('Y-m-d H:i:s');
        $this->requestContent['lan_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'id'               => 1,
                'name'             => $this->requestContent['name'],
                'tournament_start' => $this->requestContent['tournament_start'],
                'tournament_end'   => $this->requestContent['tournament_end'],
                'players_to_reach' => $this->requestContent['players_to_reach'],
                'teams_to_reach'   => $this->requestContent['teams_to_reach'],
                'teams_reached'    => 0,
                'rules'            => $this->requestContent['rules'],
                'price'            => $this->requestContent['price'],
                'teams'            => [],
                'state'            => 'hidden',
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreateHasPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testCreateLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreateNameRequired(): void
    {
        $this->requestContent['name'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'The name field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreateNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreatePriceInteger(): void
    {
        $this->requestContent['price'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreatePriceMin(): void
    {
        $this->requestContent['price'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreatePriceDefault(): void
    {
        $this->requestContent['price'] = '';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'id'               => 1,
                'name'             => $this->requestContent['name'],
                'tournament_start' => $this->requestContent['tournament_start'],
                'tournament_end'   => $this->requestContent['tournament_end'],
                'players_to_reach' => $this->requestContent['players_to_reach'],
                'teams_to_reach'   => $this->requestContent['teams_to_reach'],
                'teams_reached'    => 0,
                'rules'            => $this->requestContent['rules'],
                'price'            => 0,
                'teams'            => [],
                'state'            => 'hidden',
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateTournamentStartRequired(): void
    {
        $this->requestContent['tournament_start'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_start' => [
                        0 => 'The tournament start field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateTournamentStartAfterOrEqualLanStartTime(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->subHour()->format('Y-m-d H:i:s');
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/tournament', $this->requestContent)
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

    public function testCreateTournamentEndRequired(): void
    {
        $this->requestContent['tournament_end'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_end' => [
                        0 => 'The tournament end field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateTournamentEndBeforeOrEqualLanEndTime(): void
    {
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->addHour(0)->format('Y-m-d H:i:s');
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/tournament', $this->requestContent)
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

    public function testCreatePlayersToReachRequired(): void
    {
        $this->requestContent['players_to_reach'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'players_to_reach' => [
                        0 => 'The players to reach field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePlayersToReachMin(): void
    {
        $this->requestContent['players_to_reach'] = 0;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreatePlayersToReachInteger(): void
    {
        $this->requestContent['players_to_reach'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreateTeamsToReachRequired(): void
    {
        $this->requestContent['teams_to_reach'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'teams_to_reach' => [
                        0 => 'The teams to reach field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateTeamsToReachMin(): void
    {
        $this->requestContent['teams_to_reach'] = 0;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreateTeamsToReachInteger(): void
    {
        $this->requestContent['teams_to_reach'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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

    public function testCreateRulesRequired(): void
    {
        $this->requestContent['rules'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'rules' => [
                        0 => 'The rules field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRulesString(): void
    {
        $this->requestContent['rules'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament', $this->requestContent)
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
