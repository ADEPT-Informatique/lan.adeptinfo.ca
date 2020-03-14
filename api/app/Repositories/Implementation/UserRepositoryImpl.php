<?php

namespace App\Repositories\Implementation;

use App\Model\Tag;
use App\Model\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;

class UserRepositoryImpl implements UserRepository
{
    public function addConfirmationCode(string $email, string $confirmationCode): void
    {
        DB::table('user')
            ->where('email', $email)
            ->update([
                'confirmation_code' => $confirmationCode,
            ]);
    }

    public function addFacebookToUser(string $email, string $facebookId): void
    {
        DB::table('user')
            ->where('email', $email)
            ->update([
                'facebook_id' => $facebookId,
            ]);
    }

    public function addGoogleToUser(string $email, string $googleId): void
    {
        DB::table('user')
            ->where('email', $email)
            ->update([
                'google_id' => $googleId,
            ]);
    }

    public function confirmAccount(string $userId): void
    {
        DB::table('user')
            ->where('id', $userId)
            ->update([
                'is_confirmed'      => true,
                'confirmation_code' => null,
            ]);
    }

    public function createFacebookUser(string $facebookId, string $firstName, string $lastName, string $email): int
    {
        return DB::table('user')
            ->insertGetId([
                'facebook_id' => $facebookId,
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'email'       => $email,
            ]);
    }

    public function createGoogleUser(string $googleId, string $firstName, string $lastName, string $email): int
    {
        return DB::table('user')
            ->insertGetId([
                'google_id'  => $googleId,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
            ]);
    }

    public function createTag(
        int $userId,
        string $name
    ): int {
        return DB::table('tag')
            ->insertGetId([
                'name'    => $name,
                'user_id' => $userId,
            ]);
    }

    public function createUser(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $confirmationCode
    ): int
    {
        return DB::table('user')
            ->insertGetId([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'password'          => Hash::make($password),
                'confirmation_code' => $confirmationCode,
            ]);
    }

    public function deleteUserById(int $userId): void
    {
        User::destroy($userId);
    }

    public function findByConfirmationCode(string $confirmationCode): User
    {
        return User::where('confirmation_code', $confirmationCode)->first();
    }

    public function findByEmail(string $userEmail): ?User
    {
        return User::where('email', $userEmail)->first();
    }

    public function findById(int $userId): ?User
    {
        return User::find($userId);
    }

    public function findTagById(int $id): ?Tag
    {
        return Tag::find($id);
    }

    public function getPaginatedUsersCriteria(
        string $queryString,
        string $orderColumn,
        string $orderDirection,
        int $itemsPerPage,
        int $currentPage
    ): AbstractPaginator {
        return User::where('last_name', 'like', '%'.$queryString.'%')
            ->orWhere('first_name', 'like', '%'.$queryString.'%')
            ->orWhere('email', 'like', '%'.$queryString.'%')
            ->orderBy($orderColumn, $orderDirection)
            ->paginate($itemsPerPage, ['*'], '', $currentPage);
    }

    public function revokeRefreshToken(Token $token): void
    {
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update([
                'revoked' => true,
            ]);
    }
}
