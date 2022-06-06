<?php

namespace Vantezzen\LaravelAccountPortal;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Vantezzen\LaravelAccountPortal\Exceptions\AccountPortalNotAllowedForUserException;
use Vantezzen\LaravelAccountPortal\Exceptions\NotInAccountPortalException;
use Vantezzen\LaravelAccountPortal\PortalStorage\PortalStorage;

class LaravelAccountPortal
{
    /**
     * Open an account portal
     *
     * @param PortalStorage $storage Storage to use
     * @param Authenticatable $currentUser Currently authenticated user that wants to open the portal
     * @param Authenticatable $userForPortal User that should be portaled into
     * @throws AccountPortalNotAllowedForUserException Thrown if the current user is not allowed to open the portal
     */
    public function openPortal(PortalStorage $storage, Authenticatable $currentUser, Authenticatable $userForPortal): void
    {
        if (! $this->canUsePortal($storage, $userForPortal)) {
            throw new AccountPortalNotAllowedForUserException();
        }

        $this->switchIntoPortal($storage, $currentUser, $userForPortal);
    }

    public function canUsePortal(PortalStorage $storage, ?Authenticatable $user = null): bool
    {
        if ($storage->hasPortalInformation()) {
            return false;
        }

        return Gate::allows("use-account-portal", $user);
    }

    private function switchIntoPortal(PortalStorage $storage, Authenticatable $currentUser, Authenticatable $userForPortal): void
    {
        $storage->storePortalInformation($currentUser->getAuthIdentifier());
        $this->logIntoUser($userForPortal);
    }

    private function logIntoUser(Authenticatable $user): void
    {
        Auth::logout();
        Auth::login($user);
    }

    /**
     * Close an open portal to go back to the original user account
     *
     * Usage:
     * $portal->closePortal($request->session(), fn($id) => User::find($id));
     *
     * @param PortalStorage $storage Storage to use
     * @param callable $getUserFromId Function that returns an authenticatable user when given a user ID
     * @return void
     * @throws NotInAccountPortalException Thrown if trying to close a portal that isn't open
     */
    public function closePortal(PortalStorage $storage, callable $getUserFromId): void
    {
        if (! $this->isInPortal($storage)) {
            throw new NotInAccountPortalException();
        }

        $this->switchOutOfPortal($storage, $getUserFromId);
    }

    /**
     * Check if a session instance currently has an open portal
     *
     * @param Session $session Session to check
     * @return bool
     */
    public function isInPortal(PortalStorage $storage): bool
    {
        return $storage->hasPortalInformation();
    }

    private function switchOutOfPortal(PortalStorage $storage, callable $getUserFromId): void
    {
        $originalUser = $this->getOriginalUserFromSession($storage, $getUserFromId);
        $this->logIntoUser($originalUser);
    }

    private function getOriginalUserFromSession(PortalStorage $storage, callable $getUserFromId): Authenticatable
    {
        return $getUserFromId($storage->getAndForgetPortalInformation());
    }
}
