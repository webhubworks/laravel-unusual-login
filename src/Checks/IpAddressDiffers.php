<?php

namespace WebhubWorks\UnusualLogin\Checks;

use WebhubWorks\UnusualLogin\DTOs\CheckData;

class IpAddressDiffers extends Check
{
    public function handle(CheckData $checkData, \Closure $next): CheckData
    {
        if($checkData->currentIpAddress !== $checkData->lastUserLogin->ip_address) {
            $checkData->totalScore += $this->getScore();
        }

        return $next($checkData);
    }
}
