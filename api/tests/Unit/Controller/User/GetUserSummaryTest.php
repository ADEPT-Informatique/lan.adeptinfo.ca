<?php

namespace Tests\Unit\Controller\User;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUserSummaryTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament1;
    protected $tournament2;
    protected $tournament3;
    protected $team1;
    protected $team2;
    protected $team3;

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

        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testGetUserSummary(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament1 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(),
            'tournament_end' => $endTime->subHour(),
        ]);
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament2 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(),
            'tournament_end' => $endTime->subHour(),
        ]);
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament3 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team1->id,
            'is_leader' => true,
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team2->id,
            'is_leader' => true,
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament3->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team3->id,
            'is_leader' => false,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            factory('App\Model\Request')->create([
                'tag_id'  => $tag->id,
                'team_id' => $this->team1->id,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            factory('App\Model\Request')->create([
                'tag_id'  => $tag->id,
                'team_id' => $this->team2->id,
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            factory('App\Model\Request')->create([
                'tag_id'  => $tag->id,
                'team_id' => $this->team3->id,
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user/summary', $this->requestContent)
            ->seeJsonEquals([
                'first_name'    => $this->user->first_name,
                'last_name'     => $this->user->last_name,
                'email'         => $this->user->email,
                'request_count' => 5,
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUserSummaryCurrentLan(): void
    {
        $this->lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $this->requestContent['lan_id'] = null;

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
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament3 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team1->id,
            'is_leader' => true,
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team2->id,
            'is_leader' => true,
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament3->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->tag->id,
            'team_id'   => $this->team3->id,
            'is_leader' => false,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            factory('App\Model\Request')->create([
                'tag_id'  => $tag->id,
                'team_id' => $this->team1->id,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            factory('App\Model\Request')->create([
                'tag_id'  => $tag->id,
                'team_id' => $this->team2->id,
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id,
            ]);
            factory('App\Model\Request')->create([
                'tag_id'  => $tag->id,
                'team_id' => $this->team3->id,
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user/summary', $this->requestContent)
            ->seeJsonEquals([
                'first_name'    => $this->user->first_name,
                'last_name'     => $this->user->last_name,
                'email'         => $this->user->email,
                'request_count' => 5,
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUserSummaryLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user/summary', $this->requestContent)
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

    public function testGetUserSummaryLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user/summary', $this->requestContent)
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
