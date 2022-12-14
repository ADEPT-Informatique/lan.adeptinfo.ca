<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lanRole;
    protected $lan;

    protected $requestContent = [
        'lan_id'  => null,
        'role_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'delete-lan-role'
        );

        $permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id'       => $this->lanRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->lanRole->id,
        ]);

        $this->requestContent['role_id'] = $this->lanRole->id;
        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testDeleteLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'id'           => $this->lanRole->id,
                'name'         => $this->lanRole->name,
                'display_name' => $this->lanRole->en_display_name,
                'description'  => $this->lanRole->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testDeleteLanRoleLanIdExists(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
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

    public function testDeleteLanRoleLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '???';
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
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

    public function testDeleteLanRoleLanIdRequired(): void
    {
        $this->requestContent['lan_id'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteLanRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The role id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteLanRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '???';
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The role id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteLanRoleIdExist(): void
    {
        $this->requestContent['role_id'] = -1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The selected role id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
