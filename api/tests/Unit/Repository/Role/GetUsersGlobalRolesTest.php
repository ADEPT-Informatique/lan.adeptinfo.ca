<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUsersGlobalRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');
    }

    public function testGetUsersGlobalRoles(): void
    {
        $user = factory('App\Model\User')->create();
        $permissions = Permission::inRandomOrder()
            ->take(4)
            ->get();

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $globalRoles[$i]->id,
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $user->id,
                'role_id' => $globalRoles[$i]->id,
            ]);
        }

        $results = $this->roleRepository->getUsersGlobalRoles(
            $user->email
        );

        $this->assertEquals($globalRoles[0]->id, $results[0]->id);
        $this->assertEquals($globalRoles[0]->name, $results[0]->name);
        $this->assertEquals($globalRoles[0]->en_display_name, $results[0]->en_display_name);
        $this->assertEquals($globalRoles[0]->en_description, $results[0]->en_description);

        $this->assertEquals($globalRoles[1]->id, $results[1]->id);
        $this->assertEquals($globalRoles[1]->name, $results[1]->name);
        $this->assertEquals($globalRoles[1]->en_display_name, $results[1]->en_display_name);
        $this->assertEquals($globalRoles[1]->en_description, $results[1]->en_description);

        $this->assertEquals($globalRoles[2]->id, $results[2]->id);
        $this->assertEquals($globalRoles[2]->name, $results[2]->name);
        $this->assertEquals($globalRoles[2]->en_display_name, $results[2]->en_display_name);
        $this->assertEquals($globalRoles[2]->en_description, $results[2]->en_description);

        $this->assertEquals($globalRoles[3]->id, $results[3]->id);
        $this->assertEquals($globalRoles[3]->name, $results[3]->name);
        $this->assertEquals($globalRoles[3]->en_display_name, $results[3]->en_display_name);
        $this->assertEquals($globalRoles[3]->en_description, $results[3]->en_description);
    }
}
