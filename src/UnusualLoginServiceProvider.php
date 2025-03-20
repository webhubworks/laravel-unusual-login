<?php

namespace WebhubWorks\UnusualLogin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WebhubWorks\UnusualLogin\Commands\UnusualLoginCommand;

class UnusualLoginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-unusual-login')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_unusual_login_table')
            ->hasCommand(UnusualLoginCommand::class);
    }
}
