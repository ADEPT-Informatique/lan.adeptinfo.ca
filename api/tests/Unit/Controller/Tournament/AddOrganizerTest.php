<?php

namespace Tests\Unit\Controller\Tournament;

use App\Model\Permission;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddOrganizerTest extends TestCase
{
    use DatabaseMigrations;

    protected $organizer;
    protected $organizer2;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();

        $this->organizer = factory('App\Model\User')->create();
        $this->organizer2 = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
            'teams_to_reach' => 10,
            'players_to_reach' => 10,
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $this->addLanPermissionToUser(
            $this->organizer->id,
            $this->lan->id,
            'add-organizer'
        );
    }

    public function testAddOrganizer(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer2->email,
            ])
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

    public function testAddOrganizerTournamentIdExist(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.-1 .'/organizer', [
                'email' => $this->organizer2->email,
            ])
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

    public function testAddOrganizerHasPermissionInLanOrIsTournamentAdminPermissionSuccess(): void
    {
        $user = factory('App\Model\User')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
        $permission = Permission::where('name', 'add-organizer')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id'       => $role->id,
            'permission_id' => $permission->id,
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $user->id,
        ]);
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer2->email,
            ])
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

    public function testAddOrganizerHasPermissionInLanOrIsTournamentAdminTournamentAdminSuccess(): void
    {
        $user = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $user->id,
            'tournament_id' => $this->tournament->id,
        ]);
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer2->email,
            ])
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

    public function testAddOrganizerHasPermissionInLanOrIsTournamentAdminNoPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer2->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testAddOrganizerEmailNotCurrentUser(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'email' => [
                        0 => 'The email cannot be the same as the one used by the current user.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAddOrganizerEmailExist(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => '☭',
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'email' => [
                        0 => 'The selected email is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
