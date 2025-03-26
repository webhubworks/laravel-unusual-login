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
                'create_user_logins_table',
                'create_user_login_attempts_table',
            ])
            ->publishesServiceProvider('UnusualLoginChecksServiceProvider')
            ->hasCommand(PurgeLoginAttemptsCommand::class)
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->endWith(function(InstallCommand $command) {
                        $command->info('Please go through the config before running the migrations.');
                    });
            });
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(UnusualLogin::class);
        $this->app->alias(UnusualLogin::class, 'unusual-login');
    }

    public function bootingPackage(): void
    {
        parent::bootingPackage();

        $this->app->register(UnusualLoginEventServiceProvider::class);
    }
}
