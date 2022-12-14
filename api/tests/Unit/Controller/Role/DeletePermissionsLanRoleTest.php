<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeletePermissionsLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lanRole;
    protected $lan;
    protected $permissions;

    protected $requestContent = [
        'lan_id'      => null,
        'role_id'     => null,
        'permissions' => null,
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
            'delete-permissions-lan-role'
        );

        $this->permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        foreach ($this->permissions as $permissionId) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id'       => $this->lanRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        $this->requestContent['role_id'] = $this->lanRole->id;
        $this->requestContent['lan_id'] = $this->lan->id;
        $this->requestContent['permissions'] = collect($this->permissions)->take(5)->toArray();
    }

    public function testDeletePermissionsLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'id'           => $this->lanRole->id,
                'name'         => $this->lanRole->name,
                'display_name' => $this->lanRole->en_display_name,
                'description'  => $this->lanRole->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeletePermissionsLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testDeletePermissionsLanRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testDeletePermissionsLanRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '???';
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testDeletePermissionsLanRoleIdExist(): void
    {
        $this->requestContent['role_id'] = -1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testDeletePermissionsLanRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The permissions field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsLanRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The permissions must be an array.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsLanRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = [(string) $this->requestContent['permissions'][0], $this->requestContent['permissions'][1]];
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The array must contain only integers.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsLanRolePermissionsPermissionsDontBelongToRole(): void
    {
        $permission = factory('App\Model\Permission')->create();
        $permission->delete();
        $this->requestContent['permissions'] = collect($this->requestContent['permissions'])
            ->push($permission->id)
            ->toArray();

        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions is not attributed to this role.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
