<?php

namespace Vantezzen\LaravelAccountPortal;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Vantezzen\LaravelAccountPortal\Exceptions\AccountPortalNotAllowedForUserException;
use Vantezzen\LaravelAccountPortal\Exceptions\NotInAccountPortalException;

class LaravelAccountPortal
{
    /**
     * Open an account portal
     *
     * @param Session $session Session storage that can be used for saving portal information
     * @param Authenticatable $currentUser Currently authenticated user that wants to open the portal
     * @param Authenticatable $userForPortal User that should be portaled into
     * @throws AccountPortalNotAllowedForUserException Thrown if the current user is not allowed to open the portal
     */
    public function openPortal(Session $session, Authenticatable $currentUser, Authenticatable $userForPortal): void
    {
        if (! $this->canUsePortal($userForPortal)) {
            throw new AccountPortalNotAllowedForUserException();
        }

        $this->switchIntoPortal($session, $currentUser, $userForPortal);
    }

    public function canUsePortal(Authenticatable $user): bool
    {
        return Gate::allows("use-account-portal", $user);
    }

    private function switchIntoPortal(Session $session, Authenticatable $currentUser, Authenticatable $userForPortal): void
    {
        $session->put($this->getSessionKey(), $currentUser->id);
        $this->logIntoUser($userForPortal);
    }

    /**
     * @return Repository|Application|mixed
     */
    public function getSessionKey(): mixed
    {
        return config('account-portal.session_key');
    }

    private function logIntoUser(Authenticatable $user): void
    {
        Auth::logout();
        Auth::login($user);
    }

    /**
     * Close an open portal to go back to the original user account
     *
     * @param Session $session Session that the portal information is saved in
     * @return void
     * @throws NotInAccountPortalException Thrown if trying to close a portal that isn't open
     */
    public function closePortal(Session $session)
    {
        if (! $this->isInPortal($session)) {
            throw new NotInAccountPortalException();
        }

        $this->switchOutOfPortal($session);
    }

    /**
     * Check if a session instance currently has an open portal
     *
     * @param Session $session Session to check
     * @return bool
     */
    public function isInPortal(Session $session): bool
    {
        return $session->has($this->getSessionKey());
    }

    private function switchOutOfPortal(Session $session): void
    {
        $originalUser = $this->getOriginalUserFromSession($session);
        $this->logIntoUser($originalUser);
        $session->forget($this->getSessionKey());
    }

    private function getOriginalUserFromSession(Session $session): Authenticatable
    {
        return User::find($session->get($this->getSessionKey()));
    }
}
