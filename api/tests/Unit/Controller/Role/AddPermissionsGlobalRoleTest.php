<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddPermissionsGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $globalRole;

    protected $requestContent = [
        'role_id'     => null,
        'permissions' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'add-permissions-global-role'
        );

        $this->requestContent['role_id'] = $this->globalRole->id;
        $this->requestContent['permissions'] = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();
    }

    public function testAddPermissionsGlobalRole(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'id'           => $this->globalRole->id,
                'name'         => $this->globalRole->name,
                'display_name' => $this->globalRole->en_display_name,
                'description'  => $this->globalRole->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testAddPermissionsGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testAddPermissionsGlobalRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testAddPermissionsGlobalRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '???';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testAddPermissionsGlobalRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testAddPermissionsGlobalRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testAddPermissionsGlobalRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = [(string) $this->requestContent['permissions'][0], $this->requestContent['permissions'][1]];
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testAddPermissionsGlobalRolePermissionsElementsInArrayExistInPermission(): void
    {
        $permission = factory('App\Model\Permission')->create();
        $permission->delete();
        $this->requestContent['permissions'] = [
            $this->requestContent['permissions'][0],
            $permission->id,
        ];
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'An element of the array is not an existing permission id.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAddPermissionsGlobalRolePermissionsPermissionsDontBelongToRole(): void
    {
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id'       => $this->globalRole->id,
            'permission_id' => $this->requestContent['permissions'][0],
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions is already attributed to this role.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
