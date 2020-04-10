<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUsersLanRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');
    }

    public function testGetUsersLanRoles(): void
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(4)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id,
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $lanRoles[$i]->id,
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $user->id,
                'role_id' => $lanRoles[$i]->id,
            ]);
        }

        $results = $this->roleRepository->getUsersLanRoles(
            $user->email,
            $lan->id
        );

        $this->assertEquals($lanRoles[0]->id, $results[0]->id);
        $this->assertEquals($lanRoles[0]->name, $results[0]->name);
        $this->assertEquals($lanRoles[0]->en_display_name, $results[0]->en_display_name);
        $this->assertEquals($lanRoles[0]->en_description, $results[0]->en_description);

        $this->assertEquals($lanRoles[1]->id, $results[1]->id);
        $this->assertEquals($lanRoles[1]->name, $results[1]->name);
        $this->assertEquals($lanRoles[1]->en_display_name, $results[1]->en_display_name);
        $this->assertEquals($lanRoles[1]->en_description, $results[1]->en_description);

        $this->assertEquals($lanRoles[2]->id, $results[2]->id);
        $this->assertEquals($lanRoles[2]->name, $results[2]->name);
        $this->assertEquals($lanRoles[2]->en_display_name, $results[2]->en_display_name);
        $this->assertEquals($lanRoles[2]->en_description, $results[2]->en_description);

        $this->assertEquals($lanRoles[3]->id, $results[3]->id);
        $this->assertEquals($lanRoles[3]->name, $results[3]->name);
        $this->assertEquals($lanRoles[3]->en_display_name, $results[3]->en_display_name);
        $this->assertEquals($lanRoles[3]->en_description, $results[3]->en_description);
    }
}
