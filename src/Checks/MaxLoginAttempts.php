<?php

namespace WebhubWorks\UnusualLogin\Checks;

use WebhubWorks\UnusualLogin\DTOs\CheckData;

class MaxLoginAttempts extends Check
{
    public int $maxLoginAttempts = 2;

    public function noMoreThan(int $maxLoginAttempts): self
    {
        $this->maxLoginAttempts = $maxLoginAttempts;

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
