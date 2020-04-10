<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'tournament_id' => null,
        'user_tag_id'   => null,
        'name'          => 'WorkersUnite',
        'tag'           => 'PRO',
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
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);

        $this->requestContent['tournament_id'] = $this->tournament->id;
        $this->requestContent['user_tag_id'] = $this->tag->id;
    }

    public function testCreate(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'id'            => 1,
                'tournament_id' => $this->requestContent['tournament_id'],
                'name'          => $this->requestContent['name'],
                'tag'           => $this->requestContent['tag'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateTournamentIdRequired(): void
    {
        $this->requestContent['tournament_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The tournament id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateTournamentIdExist(): void
    {
        $this->requestContent['tournament_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
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

    public function testCreateUserTagIdRequired(): void
    {
        $this->requestContent['user_tag_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'user_tag_id' => [
                        0 => 'The user tag id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateUserTagIdExist(): void
    {
        $this->requestContent['user_tag_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'user_tag_id' => [
                        0 => 'The selected user tag id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateUserTournamentIdUniqueUserPerTournamentSameTag(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', [
                'tournament_id' => $this->tournament->id,
                'user_tag_id'   => $this->tag->id,
                'name'          => 'name',
                'tag'           => 'tag',
            ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'A user can only be once in a tournament.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateUserTournamentIdUniqueUserPerTournamentSameUser(): void
    {
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', [
                'tournament_id' => $this->tournament->id,
                'user_tag_id'   => $tag->id,
                'name'          => 'name',
                'tag'           => 'tag',
            ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'A user can only be once in a tournament.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateUserTagIdUniqueUserPerTournamentSameLan(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', [
                'tournament_id' => $tournament->id,
                'user_tag_id'   => $this->tag->id,
                'name'          => 'name',
                'tag'           => 'tag',
            ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'id'            => 2,
                'tournament_id' => $this->requestContent['tournament_id'],
                'name'          => $this->requestContent['name'],
                'tag'           => $this->requestContent['tag'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateUserTagIdTagBelongsToUser(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $this->requestContent['user_tag_id'] = $tag->id;

        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testCreateNameRequired(): void
    {
        $this->requestContent['name'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'The name field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'The name must be a string.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'The name may not be greater than 255 characters.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameUniqueTeamNamePerTournament(): void
    {
        factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
            'name'          => $this->requestContent['name'],
        ]);

        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'name' => [
                        0 => 'A team name must be unique per lan.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameUniqueTeamNamePerTournamentSameLan(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', [
                'tournament_id' => $tournament->id,
                'user_tag_id'   => $this->tag->id,
                'name'          => $this->requestContent['name'],
                'tag'           => 'tag',
            ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'id'            => 2,
                'tournament_id' => $this->requestContent['tournament_id'],
                'name'          => $this->requestContent['name'],
                'tag'           => $this->requestContent['tag'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateTagString(): void
    {
        $this->requestContent['tag'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag' => [
                        0 => 'The tag must be a string.',
                    ],
                ],
            ]);
    }

    public function testCreateTagMaxLength(): void
    {
        $this->requestContent['tag'] = str_repeat('☭', 6);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag' => [
                        0 => 'The tag may not be greater than 5 characters.',
                    ],
                ],
            ]);
    }

    public function testCreateTagUniqueTeamTagPerTournament(): void
    {
        factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
            'tag'           => $this->requestContent['tag'],
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag' => [
                        0 => 'A team tag must be unique per lan.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateTagUniqueTeamTagPerTournamentSameLan(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(0),
            'tournament_end' => $endTime->subHour(),
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', [
                'tournament_id' => $tournament->id,
                'user_tag_id'   => $this->tag->id,
                'name'          => 'name',
                'tag'           => $this->requestContent['tag'],
            ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/team', $this->requestContent)
            ->seeJsonEquals([
                'id'            => 2,
                'tournament_id' => $this->requestContent['tournament_id'],
                'name'          => $this->requestContent['name'],
                'tag'           => $this->requestContent['tag'],
            ])
            ->assertResponseStatus(201);
    }
}
