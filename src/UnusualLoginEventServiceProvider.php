<?php

namespace WebhubWorks\UnusualLogin;

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use WebhubWorks\UnusualLogin\Listener\LoginListener;

class UnusualLoginEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LoginListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
