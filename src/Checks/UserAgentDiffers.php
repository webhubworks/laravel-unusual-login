<?php

namespace WebhubWorks\UnusualLogin\Checks;

use WebhubWorks\UnusualLogin\DTOs\CheckData;

class UserAgentDiffers extends Check
{
    public function handle(CheckData $checkData, \Closure $next): CheckData
    {
        if($checkData->currentUserAgent !== $checkData->lastUserLogin->user_agent) {
            $checkData->totalScore += $this->getScore();
        }

        return $next($checkData);
    }
}
