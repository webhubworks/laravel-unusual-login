<?php

namespace WebhubWorks\UnusualLogin;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class UnusualLoginChecksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /**
         * Checks to be performed on login.
         * Those checks a run through a pipeline, so make sure to `return $next($data)` at the end of each check.
         * Each check has a score, which is added up to determine if an unusual login is detected.
         */
        UnusualLogin::checks([
            \WebhubWorks\UnusualLogin\Checks\IpAddressDiffers::withScore(25),
            \WebhubWorks\UnusualLogin\Checks\UserAgentDiffers::withScore(25),
            \WebhubWorks\UnusualLogin\Checks\MaxLoginAttempts::withScore(50)
                ->userIdentifiesVia('email')
                ->resetLoginAttemptsAfterMinutes(60 * 24)
                ->noMoreThan(2),
        ])

            /**
             * Threshold to trigger an unusual login event.
             * This value is the sum of all checks' scores.
             * If threshold is reached, UnusualLoginDetected::class is dispatched.
             */
            ->threshold(50);
    }
}
