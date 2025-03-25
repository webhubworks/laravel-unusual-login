<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Illuminate\Auth\Events\Attempting;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;

class LoginAttemptListener
{
    public function handle(Attempting $event): void
    {
        $identifier = $event->credentials[config('unusual-login.user_identifies_via')] ?? null;

        if(! $identifier) {
            return;
        }

        $userLoginAttempt = UserLoginAttempt::where([
            'identifier' => $identifier,
        ])->first();

        if(! $userLoginAttempt) {
            UserLoginAttempt::create([
                'identifier' => $identifier,
                'attempts' => 1,
            ]);

            return;
        }

        $userLoginAttempt->increment('attempts');
    }
}
