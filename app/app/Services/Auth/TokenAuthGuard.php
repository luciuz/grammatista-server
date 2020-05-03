<?php

namespace App\Services\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * Class TokenAuthGuard
 * @package App\Services\Auth
 */
class TokenAuthGuard implements Guard
{
    use GuardHelpers;

    private const TOKEN_KEY = 'token';

    /**
     * The request instance.
     *
     * @var Request
     */
    private $request;

    /**
     * Create a new authentication guard.
     *
     * @param UserProvider $provider
     * @param Request      $request
     */
    public function __construct(
        UserProvider $provider,
        Request $request
    ) {
        $this->request = $request;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if ($this->user !== null) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();

        if (! empty($token)) {
            $user = $this->provider->retrieveByCredentials([self::TOKEN_KEY => $token]);
        }

        return $this->user = $user;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest(): ?string
    {
        return $this->request->header(self::TOKEN_KEY);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials[self::TOKEN_KEY])) {
            return false;
        }

        $credentials = [self::TOKEN_KEY => $credentials[self::TOKEN_KEY]];

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }
}
