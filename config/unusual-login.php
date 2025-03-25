<?php

return [

    /**
     * The field, with which the user identifies
     * and the app attempts to log in.
     */
    'user_identifies_via' => 'email',

    /**
     * Notification class to be dispatched when an unusual login is detected.
     * Uncomment the line below to enable.
     */
    // 'notification' => \WebhubWorks\UnusualLogin\Notifications\UnusualLoginDetectedNotification::class,

    /**
     * Checks to be performed on login.
     * Those checks a run through a pipeline, so make sure to `return $next($data)` at the end of each check.
     * Each check has a score, which is added up to determine if an unusual login is detected.
     */
    'checks' => [
        // pre-built checks
        \WebhubWorks\UnusualLogin\Checks\IpAddressDiffers::withScore(25),
        \WebhubWorks\UnusualLogin\Checks\UserAgentDiffers::withScore(25),
        \WebhubWorks\UnusualLogin\Checks\MaxLoginAttempts::withScore(50)->noMoreThan(2),
        // custom checks
        //NoSimultaneousSessions::withScore(50),
    ],

    /**
     * Threshold to trigger an unusual login event.
     * This value is the sum of all checks' scores.
     */
    'threshold' => 50,

    /**
     * If threshold is reached:
     * Dispatches event UnusualLoginDetected::class with payload:
     * - user model
     * - current browser, browser version, OS
     * - current IP address
     * - num. of simultaneous sessions
     * - login timestamp
     */
];
