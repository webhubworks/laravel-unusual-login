<?php

namespace WebhubWorks\UnusualLogin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaxLoginAttemptsDetected
{
    use SerializesModels;
    use Dispatchable;

    public function __construct(
        public string $userIdentifier,
        public int $loginAttempts
    ) {
        //
    }
}
