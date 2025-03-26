<?php

namespace WebhubWorks\UnusualLogin;

use Illuminate\Support\Collection;
use WebhubWorks\UnusualLogin\Checks\Check;

class UnusualLogin {

    /**
     * Checks to be performed on login.
     * Those checks a run through a pipeline, so make sure to `return $next($data)` at the end of each check.
     * Each check has a score, which is added up to determine if an unusual login is detected.
     */
    protected array $checks = [];

    /**
     * Threshold to trigger an unusual login event.
     * This value is the sum of all checks' scores.
     * If threshold is reached, UnusualLoginDetected::class is dispatched.
     */
    protected int $threshold = 0;

    public static function checks(array $checks): self
    {
        $self = new static();

        $self->ensureCheckInstances($checks);

        $self->checks = array_merge($self->checks, $checks);

        return $self;
    }

    public function threshold(int $threshold): self
    {
        $this->threshold = $threshold;

        return $this;
    }

    public function getChecks(): Collection
    {
        return collect($this->checks);
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    protected function ensureCheckInstances(array $checks): void
    {
        foreach ($checks as $check) {
            if (! $check instanceof Check) {
                throw new \Exception('Invalid check. Must extend WebhubWorks\UnusualLogin\Checks\Check.');
            }
        }
    }
}
