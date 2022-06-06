<?php

namespace Vantezzen\LaravelAccountPortal\PortalStorage;

interface PortalStorage
{
    /**
     * Store information for the current session/user
     *
     * @param $portalInformation mixed Information to store
     * @return void
     */
    public function storePortalInformation(mixed $portalInformation): void;

    /**
     * Get info if the storage has portal information stored
     *
     * @return bool
     */
    public function hasPortalInformation(): bool;

    /**
     * Get the portal information stored and remove the information from the storage
     *
     * @return mixed
     */
    public function getAndForgetPortalInformation(): mixed;
}
