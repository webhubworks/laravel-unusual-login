<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Illuminate\Auth\Events\Attempting;
use WebhubWorks\UnusualLogin\Concerns\WorksWithLoginAttempts;
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
            UserLoginAttempt::create([
                'identifier' => $event->credentials[$identifier],
                'attempts' => 1,
            ]);

            return;
        }

        $resetLoginAttemptsAfterMinutes = $this->getResetLoginAttemptsAfterMinutes();
        if( $userLoginAttempt->updated_at->diffInMinutes(now()) > $resetLoginAttemptsAfterMinutes) {
            $userLoginAttempt->update([
                'attempts' => 1,
            ]);

            return;
        }

        $userLoginAttempt->increment('attempts');
    }
}
