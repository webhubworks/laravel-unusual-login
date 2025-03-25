<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Illuminate\Auth\Events\Attempting;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;

class LoginAttemptListener
{
    public function handle(Attempting $event): void
    {
        $identifier = $event->credentials[config('unusual-login.login_attempts.user_identifies_via')] ?? null;

        if(! $identifier) {
            return;
        }

        /** @var UserLoginAttempt $userLoginAttempt */
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
        
        if( $userLoginAttempt->updated_at->diffInMinutes(now()) > config('unusual-login.login_attempts.reset_login_attempts_after_minutes')) {
            $userLoginAttempt->update([
                'attempts' => 1,
            ]);

            return;
        }

        $userLoginAttempt->increment('attempts');
    }
}
