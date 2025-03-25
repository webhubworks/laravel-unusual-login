<?php

namespace WebhubWorks\UnusualLogin\Checks;

use WebhubWorks\UnusualLogin\DTOs\CheckData;

class MaxLoginAttempts extends Check
{
    public int $maxLoginAttempts = 2;

    /**
     * The database field, with which the user identifies and the app attempts to log in.
     */
    public string $userIdentifiesVia = 'email';

    /**
     * In case a login fails and the user tries after a long time,
     * we should not consider the previous attempts.
     */
    public int $resetLoginAttemptsAfterMinutes = 60 * 24; // 24 hours

    public function noMoreThan(int $maxLoginAttempts): self
    {
        $this->maxLoginAttempts = $maxLoginAttempts;

        return $this;
    }

    public function userIdentifiesVia(string $userIdentifiesVia): self
    {
        $this->userIdentifiesVia = $userIdentifiesVia;

        return $this;
    }

    public function resetLoginAttemptsAfterMinutes(int $resetLoginAttemptsAfterMinutes): self
    {
        $this->resetLoginAttemptsAfterMinutes = $resetLoginAttemptsAfterMinutes;

        return $this;
    }

    public function handle(CheckData $checkData, \Closure $next): CheckData
    {
        if($checkData->loginAttempts > $this->maxLoginAttempts) {
            $checkData->totalScore += $this->getScore();
        }

        return $next($checkData);
    }
}
