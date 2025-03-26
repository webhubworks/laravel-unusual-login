<?php

namespace WebhubWorks\UnusualLogin;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use WebhubWorks\UnusualLogin\Commands\PurgeLoginAttemptsCommand;

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
            ->hasMigrations([
                '2025_03_20_100000_create_user_logins_table',
                '2025_03_20_100001_create_user_login_attempts_table',
            ])
            ->hasCommand(PurgeLoginAttemptsCommand::class)
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->endWith(function(InstallCommand $command) {
                        $command->info('Please go through the config before running the migrations.');
                    });
            });
    }

    public function bootingPackage(): void
    {
        parent::bootingPackage();

        $this->app->register(UnusualLoginEventServiceProvider::class);
    }
}
