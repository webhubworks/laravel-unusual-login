<?php

return [

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
        \WebhubWorks\UnusualLogin\Checks\IpAddressDiffers::withScore(25),
        \WebhubWorks\UnusualLogin\Checks\UserAgentDiffers::withScore(25),
        \WebhubWorks\UnusualLogin\Checks\MaxLoginAttempts::withScore(50)->noMoreThan(2),
    ],

    /**
     * Threshold to trigger an unusual login event.
     * This value is the sum of all checks' scores.
     * If threshold is reached, UnusualLoginDetected::class is dispatched.
     */
    'threshold' => 50,

    'login_attempts' => [
        /**
         * The field, with which the user identifies
         * and the app attempts to log in.
         */
        'user_identifies_via' => 'email',

        /**
         * In case a login fails and the user tries after a long time,
         * we should not consider the previous attempts.
         */
        'reset_login_attempts_after_minutes' => 60 * 60 * 24, // 24 hours
    ],
];
