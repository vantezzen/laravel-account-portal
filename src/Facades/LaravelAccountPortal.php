<?php

namespace Vantezzen\LaravelAccountPortal\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vantezzen\LaravelAccountPortal\LaravelAccountPortal
 */
class LaravelAccountPortal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-account-portal';
    }
}
