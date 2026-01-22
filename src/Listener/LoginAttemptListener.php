<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Illuminate\Auth\Events\Attempting;
use WebhubWorks\UnusualLogin\Concerns\WorksWithLoginAttempts;
use WebhubWorks\UnusualLogin\Events\MaxLoginAttemptsDetected;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;

class LoginAttemptListener
{
    use WorksWithLoginAttempts;

    public function handle(Attempting $event): void
    {
        $identifier = $this->getUserIdentifiesVia();
        if(! $identifier) {
            return;
        }

        /** @var UserLoginAttempt $userLoginAttempt */
        $userLoginAttempt = UserLoginAttempt::where([
            'identifier' => $event->credentials[$identifier],
        ])->first();

        if(! $userLoginAttempt) {
            $userLoginAttempt = UserLoginAttempt::create([
                'identifier' => $event->credentials[$identifier],
                'attempts' => 0,
            ]);
        }

        $resetLoginAttemptsAfterMinutes = $this->getResetLoginAttemptsAfterMinutes();
        if( $userLoginAttempt->updated_at->diffInMinutes(now()) > $resetLoginAttemptsAfterMinutes) {
            $userLoginAttempt->update([
                'attempts' => 0,
            ]);
        }

        $userLoginAttempt->increment('attempts');
        $userLoginAttempt->refresh();

        if($userLoginAttempt->attempts >= config('unusual-login.max_login_attempts')) {
            MaxLoginAttemptsDetected::dispatch($userLoginAttempt->identifier, $userLoginAttempt->attempts);
        }
    }
}
