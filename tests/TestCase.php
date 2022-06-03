<?php

namespace Vantezzen\LaravelAccountPortal\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Vantezzen\LaravelAccountPortal\LaravelAccountPortalServiceProvider;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app)
    {
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
