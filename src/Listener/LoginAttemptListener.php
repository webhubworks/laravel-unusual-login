<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Illuminate\Auth\Events\Attempting;
use WebhubWorks\UnusualLogin\Events\MaxLoginAttemptsDetected;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;

class LoginAttemptListener
{
    public function handle(Attempting $event): void
    {
        $guards = config('unusual-login.guards');
        if (! in_array($event->guard, $guards)) {
            return;
        }
        
        $identifier = $event->credentials[config('unusual-login.user_identifier_field')] ?? null;
        if(! $identifier) {
            return;
        }

        /** @var UserLoginAttempt $userLoginAttempt */
        $userLoginAttempt = UserLoginAttempt::where([
            'identifier' => $identifier,
        ])->first();

        if(! $userLoginAttempt) {
            $userLoginAttempt = UserLoginAttempt::create([
                'identifier' => $identifier,
                'attempts' => 0,
            ]);
        }

        $resetLoginAttemptsAfterMinutes = config('unusual-login.reset_login_attempts_after_minutes');
        if( $userLoginAttempt->updated_at->diffInMinutes(now()) > $resetLoginAttemptsAfterMinutes) {
            $userLoginAttempt->update([
                'attempts' => 0,
            ]);
        }

        $userLoginAttempt->increment('attempts');
        $userLoginAttempt->refresh();

        if($userLoginAttempt->attempts > config('unusual-login.max_login_attempts')) {
            MaxLoginAttemptsDetected::dispatch($userLoginAttempt->identifier, $userLoginAttempt->attempts);
        }
    }
}
