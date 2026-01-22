<?php

return [

    /**
     * The field used to identify the user during login attempts.
     */
    'user_identifier_field' => env('UNUSUAL_LOGIN_USER_IDENTIFIER_FIELD', 'email'),

    /**
     * In case a login fails and the user tries after a long time,
     * we should not consider the previous attempts.
     */
    'reset_login_attempts_after_minutes' => (int) env('UNUSUAL_LOGIN_RESET_LOGIN_ATTEMPTS_AFTER_MINUTES', 60 * 24),

    /**
     * Maximum number of login attempts.
     * When this number is exceeded, the event MaxLoginAttemptsDetected::class is dispatched.
     */
    'max_login_attempts' => (int) env('UNUSUAL_LOGIN_MAX_LOGIN_ATTEMPTS', 5),

    /**
     * Notification class to be dispatched when an unusual login is detected.
     * It will receive a \WebhubWorks\UnusualLogin\DTOs\CheckData object.
     */
    // 'notification' => \WebhubWorks\UnusualLogin\Notifications\UnusualLoginDetectedNotification::class,
];
