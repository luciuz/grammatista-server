<?php

namespace App\Services\Auth;

use App\Services\SessionService;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class TokenUserProvider
 * @package App\Services\Auth
 */
class TokenUserProvider implements UserProvider
{
    private const TOKEN_KEY = 'token';

    /** @var SessionService */
    private $sessionService;

    /**
     * TokenUserProvider constructor.
     * @param SessionService $sessionService
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     * @return Authenticatable|null
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $user
     * @param string          $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $token = $credentials[self::TOKEN_KEY];
        $sessionData = $this->sessionService->findSession($token);
        if ($sessionData !== null) {
            $user = new AuthUser();
            $user->setId($sessionData['app_user_id']);
            return $user;
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array           $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return true;
    }
}
