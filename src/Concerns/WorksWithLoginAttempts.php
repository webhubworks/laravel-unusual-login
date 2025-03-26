<?php

namespace WebhubWorks\UnusualLogin\Concerns;

use WebhubWorks\UnusualLogin\Checks\MaxLoginAttempts;
use WebhubWorks\UnusualLogin\Facades\UnusualLogin;

trait WorksWithLoginAttempts
{
    private function getUserIdentifiesVia(): ?string
    {
        /** @var MaxLoginAttempts $maxLoginAttemptsCheck */
        $maxLoginAttemptsCheck = UnusualLogin::getChecks()->first(function($check) {
            return $check instanceof MaxLoginAttempts;
        });

        return $maxLoginAttemptsCheck?->userIdentifiesVia ?? null;
    }

    private function getResetLoginAttemptsAfterMinutes(): int
    {
        /** @var MaxLoginAttempts $maxLoginAttemptsCheck */
        $maxLoginAttemptsCheck = UnusualLogin::getChecks()->first(function($check) {
            return $check instanceof MaxLoginAttempts;
        });

        return $maxLoginAttemptsCheck?->resetLoginAttemptsAfterMinutes
            ?? (new MaxLoginAttempts)->resetLoginAttemptsAfterMinutes;
    }
}
