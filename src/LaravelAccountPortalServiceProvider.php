<?php

namespace Vantezzen\LaravelAccountPortal;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vantezzen\LaravelAccountPortal\Commands\LaravelAccountPortalCommand;

class LaravelAccountPortalServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-account-portal')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-account-portal_table')
            ->hasCommand(LaravelAccountPortalCommand::class);
    }
}
