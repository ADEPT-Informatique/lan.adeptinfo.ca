<?php

namespace Tests\Unit\Service\Team;

use App\Model\Permission;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $organizer;
    protected $lan;
    protected $tournament;
    protected $team;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

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
        $this->organizer = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id'  => $this->organizer->id,
            'tournament_id' => $this->tournament->id,
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
        $permission = Permission::where('name', 'delete-team')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id'       => $role->id,
            'permission_id' => $permission->id,
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->organizer->id,
        ]);
    }

    public function testDelete(): void
    {
        $result = $this->teamService->delete($this->team->id);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }
}
