<?php

namespace Vantezzen\LaravelAccountPortal\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Gate;
use Orchestra\Testbench\TestCase as Orchestra;
use Vantezzen\LaravelAccountPortal\LaravelAccountPortalServiceProvider;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app)
    {
        Gate::define("use-account-portal", function ($currentUser = null, $portalUser = null) {
            return str_ends_with($portalUser->email, "@allowed.com");
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Vantezzen\\LaravelAccountPortal\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelAccountPortalServiceProvider::class,
        ];
    }
}
