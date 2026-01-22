<?php

return [

    /**
     * Notification class to be dispatched when an unusual login is detected.
     * It will receive a \WebhubWorks\UnusualLogin\DTOs\CheckData object.
     * Uncomment the line below to enable.
     */
    // 'notification' => \WebhubWorks\UnusualLogin\Notifications\UnusualLoginDetectedNotification::class,

    /**
     * Maximum number of login attempts.
     * When this number is reached, the even MaxLoginAttemptsDetected::class is dispatched.
     */
    'max_login_attempts' => (int) env('MAX_LOGIN_ATTEMPTS', 5),
];
