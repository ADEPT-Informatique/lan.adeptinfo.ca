<?php

namespace Tests\Unit\Controller\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RemoveOrganizerTest extends TestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $organizer;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = factory('App\Model\User')->create();
        $this->organizer = factory('App\Model\User')->create();
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
            $this->admin->id,
            $this->lan->id,
            'remove-organizer'
        );
    }

    public function testRemoveOrganizer(): void
    {
        $organizer2 = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $organizer2->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $this->actingAs($this->admin)
            ->json(
                'DELETE',
                'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer',
                ['email' => $this->organizer->email])
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

    public function testRemoveOrganizerLastOrganizer(): void
    {
        $this->actingAs($this->admin)
            ->json(
                'DELETE',
                'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer',
                ['email' => $this->organizer->email])
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

    public function testRemoveOrganizerTournamentIdExist(): void
    {
        $this->actingAs($this->admin)
            ->json(
                'DELETE',
                'http://'.env('API_DOMAIN').'/tournament/'.-1 .'/organizer',
                ['email' => $this->organizer->email])
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

    public function testRemoveOrganizerHasPermissionInLanOrIsTournamentAdminPermissionSuccess(): void
    {
        $user = factory('App\Model\User')->create();

        $this->addLanPermissionToUser(
            $user->id,
            $this->lan->id,
            'remove-organizer'
        );

        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer->email,
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

    public function testRemoveOrganizerHasPermissionInLanOrIsTournamentAdminTournamentAdminSuccess(): void
    {
        $user = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $user->id,
            'tournament_id' => $this->tournament->id,
        ]);
        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer->email,
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

    public function testRemoveOrganizerHasPermissionInLanOrIsTournamentAdminNoPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
                'email' => $this->organizer->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testRemoveOrganizerEmailNotCurrentUser(): void
    {
        $this->actingAs($this->organizer)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
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

    public function testRemoveOrganizerEmailExist(): void
    {
        $this->actingAs($this->organizer)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/tournament/'.$this->tournament->id.'/organizer', [
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
