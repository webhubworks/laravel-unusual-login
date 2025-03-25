<?php

namespace WebhubWorks\UnusualLogin\Concerns;

use WebhubWorks\UnusualLogin\Checks\MaxLoginAttempts;

trait WorksWithLoginAttempts
{
    private function getUserIdentifiesVia(): ?string
    {
        $checks = config('unusual-login.checks');

        /** @var MaxLoginAttempts $maxLoginAttemptsCheck */
        $maxLoginAttemptsCheck = collect($checks)->first(function($check) {
            return $check instanceof MaxLoginAttempts;
        });

        return $maxLoginAttemptsCheck?->userIdentifiesVia ?? null;
    }

    private function getResetLoginAttemptsAfterMinutes(): int
    {
        $checks = config('unusual-login.checks');

        /** @var MaxLoginAttempts $maxLoginAttemptsCheck */
        $maxLoginAttemptsCheck = collect($checks)->first(function($check) {
            return $check instanceof MaxLoginAttempts;
        });

        return $maxLoginAttemptsCheck?->resetLoginAttemptsAfterMinutes
            ?? (new MaxLoginAttempts)->resetLoginAttemptsAfterMinutes;
    }
}
