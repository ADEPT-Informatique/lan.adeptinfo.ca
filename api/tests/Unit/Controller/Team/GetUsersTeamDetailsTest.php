<?php

namespace Tests\Unit\Controller\Team;

use App\Model\TagTeam;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUsersTeamDetailsTest extends TestCase
{
    use DatabaseMigrations;

    protected $requestContent = [
        'team_id' => null,
    ];

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $tagTeam;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
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
        $this->tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'  => $this->tag->id,
            'team_id' => $this->team->id,
        ]);

        $this->requestContent['team_id'] = $this->team->id;
    }

    public function testGetUsersTeamDetailsAdminRequests(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'    => $tag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id,
        ]);
        $tagTeam2 = factory('App\Model\Request')->create([
            'tag_id'  => $tag2->id,
            'team_id' => $this->team->id,
        ]);

        $this->actingAs($user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/details', $this->requestContent)
            ->seeJsonEquals([
                'id'        => $this->team->id,
                'name'      => $this->team->name,
                'team_tag'  => $this->team->tag,
                'user_tags' => [
                    [
                        'id'         => $tagTeam->id,
                        'tag_id'     => $tag->id,
                        'tag_name'   => $tag->name,
                        'first_name' => $user->first_name,
                        'last_name'  => $user->last_name,
                        'is_leader'  => true,
                    ],
                    [
                        'id'         => $this->tagTeam->id,
                        'tag_id'     => $this->tag->id,
                        'tag_name'   => $this->tag->name,
                        'first_name' => $this->user->first_name,
                        'last_name'  => $this->user->last_name,
                        'is_leader'  => false,
                    ],
                ],
                'requests' => [
                    [
                        'id'         => $tagTeam2->id,
                        'tag_id'     => $tag2->id,
                        'tag_name'   => $tag2->name,
                        'first_name' => $user2->first_name,
                        'last_name'  => $user2->last_name,
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersTeamDetailsAdminNoRequests(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'    => $tag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $this->actingAs($user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/details', $this->requestContent)
            ->seeJsonEquals([
                'id'        => $this->team->id,
                'name'      => $this->team->name,
                'team_tag'  => $this->team->tag,
                'user_tags' => [
                    [
                        'id'         => $tagTeam->id,
                        'tag_id'     => $tag->id,
                        'tag_name'   => $tag->name,
                        'first_name' => $user->first_name,
                        'last_name'  => $user->last_name,
                        'is_leader'  => true,
                    ],
                    [
                        'id'         => $this->tagTeam->id,
                        'tag_id'     => $this->tag->id,
                        'tag_name'   => $this->tag->name,
                        'first_name' => $this->user->first_name,
                        'last_name'  => $this->user->last_name,
                        'is_leader'  => false,
                    ],
                ],
                'requests' => [],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersTeamDetailsNotAdmin(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/details', $this->requestContent)
            ->seeJsonEquals([
                'id'        => $this->team->id,
                'name'      => $this->team->name,
                'team_tag'  => $this->team->tag,
                'user_tags' => [
                    [
                        'id'         => $this->tagTeam->id,
                        'tag_id'     => $this->tag->id,
                        'tag_name'   => $this->tag->name,
                        'first_name' => $this->user->first_name,
                        'last_name'  => $this->user->last_name,
                        'is_leader'  => false,
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersTeamDetailsTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/details', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The team id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersTeamDetailsTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/details', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The selected team id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersTeamDetailsTeamIdUserBelongsInTeam(): void
    {
        TagTeam::where('team_id', $this->team->id)
            ->where('tag_id', $this->tag->id)
            ->delete();
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/details', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}
