<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetRequestsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament1;
    protected $tournament2;
    protected $team1;
    protected $team2;
    protected $team3;
    protected $team4;
    protected $request1;
    protected $request2;
    protected $request3;

    protected $requestContent = [
        'lan_id' => null,
    ];

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
        $this->tournament1 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament2 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id,
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id,
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id,
        ]);
        $this->team4 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id,
        ]);

        $this->request1 = factory('App\Model\Request')->create([
            'tag_id'  => $this->tag->id,
            'team_id' => $this->team1->id,
        ]);
        $this->request2 = factory('App\Model\Request')->create([
            'tag_id'  => $this->tag->id,
            'team_id' => $this->team2->id,
        ]);
        $this->request3 = factory('App\Model\Request')->create([
            'tag_id'  => $this->tag->id,
            'team_id' => $this->team4->id,
        ]);

        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testGetRequests(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'              => $this->request1->id,
                    'tag_id'          => $this->tag->id,
                    'tag_name'        => $this->tag->name,
                    'team_id'         => $this->team1->id,
                    'team_tag'        => $this->team1->tag,
                    'team_name'       => $this->team1->name,
                    'tournament_id'   => $this->tournament1->id,
                    'tournament_name' => $this->tournament1->name,
                ],
                [
                    'id'              => $this->request2->id,
                    'tag_id'          => $this->tag->id,
                    'tag_name'        => $this->tag->name,
                    'team_id'         => $this->team2->id,
                    'team_tag'        => $this->team2->tag,
                    'team_name'       => $this->team2->name,
                    'tournament_id'   => $this->tournament1->id,
                    'tournament_name' => $this->tournament1->name,
                ],
                [
                    'id'              => $this->request3->id,
                    'tag_id'          => $this->tag->id,
                    'tag_name'        => $this->tag->name,
                    'team_id'         => $this->team4->id,
                    'team_tag'        => $this->team4->tag,
                    'team_name'       => $this->team4->name,
                    'tournament_id'   => $this->tournament2->id,
                    'tournament_name' => $this->tournament2->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetRequestsCurrentLan(): void
    {
        $this->lan->is_current = true;
        $this->lan->save();
        $this->requestContent['lan_id'] = null;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
            ->seeJsonEquals([
                [
                    'id'              => $this->request1->id,
                    'tag_id'          => $this->tag->id,
                    'tag_name'        => $this->tag->name,
                    'team_id'         => $this->team1->id,
                    'team_tag'        => $this->team1->tag,
                    'team_name'       => $this->team1->name,
                    'tournament_id'   => $this->tournament1->id,
                    'tournament_name' => $this->tournament1->name,
                ],
                [
                    'id'              => $this->request2->id,
                    'tag_id'          => $this->tag->id,
                    'tag_name'        => $this->tag->name,
                    'team_id'         => $this->team2->id,
                    'team_tag'        => $this->team2->tag,
                    'team_name'       => $this->team2->name,
                    'tournament_id'   => $this->tournament1->id,
                    'tournament_name' => $this->tournament1->name,
                ],
                [
                    'id'              => $this->request3->id,
                    'tag_id'          => $this->tag->id,
                    'tag_name'        => $this->tag->name,
                    'team_id'         => $this->team4->id,
                    'team_tag'        => $this->team4->tag,
                    'team_name'       => $this->team4->name,
                    'tournament_id'   => $this->tournament2->id,
                    'tournament_name' => $this->tournament2->name,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetRequestsLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
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

    public function testGetRequestsLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/team/request', $this->requestContent)
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
}
