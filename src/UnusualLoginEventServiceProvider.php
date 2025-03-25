<?php

namespace WebhubWorks\UnusualLogin;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use WebhubWorks\UnusualLogin\Listener\LoginAttemptListener;
use WebhubWorks\UnusualLogin\Listener\LoginListener;

class UnusualLoginEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Attempting::class => [
            LoginAttemptListener::class,
        ],
        Login::class => [
            LoginListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
