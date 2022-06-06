<?php

namespace Vantezzen\LaravelAccountPortal\PortalStorage;

use Illuminate\Contracts\Session\Session;

class SessionPortalStorage implements PortalStorage
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function storePortalInformation(mixed $portalInformation): void
    {
        $this->session->put($this->getSessionKey(), $portalInformation);
    }

    private function getSessionKey(): string
    {
        return config('account-portal.session_key');
    }

    public function hasPortalInformation(): bool
    {
        return $this->session->has($this->getSessionKey());
    }

    public function getAndForgetPortalInformation(): mixed
    {
        $information = $this->session->get($this->getSessionKey());
        $this->session->forget($this->getSessionKey());

        return $information;
    }
}
