<?php

namespace WebhubWorks\UnusualLogin\Checks;

use WebhubWorks\UnusualLogin\DTOs\CheckData;

abstract class Check
{
    protected int $score = 0;

    public static function withScore(int $score): static
    {
        $self = new static();

        $self->score = $score;

        return $self;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    abstract public function handle(CheckData $checkData, \Closure $next): CheckData;
}
