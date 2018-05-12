<?php

use App\Model\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

    protected $paramsContent = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'Passw0rd!'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');
    }

    public function testSignUp()
    {
        $this->userRepository->createUser(
            $this->paramsContent['first_name'],
            $this->paramsContent['last_name'],
            $this->paramsContent['email'],
            $this->paramsContent['password']
        );
        $this->seeInDatabase('user', [
            'first_name' => $this->paramsContent['first_name'],
            'last_name' => $this->paramsContent['last_name'],
            'email' => $this->paramsContent['email'],
        ]);
    }

    public function testSignUpUniqueEmailConstraint()
    {
        $user = new User();
        $user->first_name = $this->paramsContent['first_name'];
        $user->last_name = $this->paramsContent['last_name'];
        $user->email = $this->paramsContent['email'];
        $user->password = Hash::make($this->paramsContent['password']);
        $user->save();
        try {
            $this->userRepository->createUser(
                $this->paramsContent['first_name'],
                $this->paramsContent['last_name'],
                $this->paramsContent['email'],
                $this->paramsContent['password']
            );
        } catch (QueryException $e){
            $this->assertContains('Duplicate entry \'' . $this->paramsContent['email'] . '\'', $e->getMessage());
        }
    }

}
