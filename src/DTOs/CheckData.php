<?php

namespace WebhubWorks\UnusualLogin\DTOs;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use WebhubWorks\UnusualLogin\Models\UserLogin;

class CheckData
{
    public Authenticatable $user;

    public string $currentIpAddress;

    public string $currentUserAgent;

    public UserLogin $lastUserLogin;

    public int $loginAttempts;

    public int $totalScore;

    public Carbon $loggedInAt;

    public static function make(
        Authenticatable $user,
        string          $currentIpAddress,
        string          $currentUserAgent,
        UserLogin       $lastUserLogin,
        int             $loginAttempts,
        int             $totalScore,
        Carbon          $loggedInAt,
    ): self
    {
        $self = new self();

        $self->user = $user;
        $self->currentIpAddress = $currentIpAddress;
        $self->currentUserAgent = $currentUserAgent;
        $self->lastUserLogin = $lastUserLogin;
        $self->loginAttempts = $loginAttempts;
        $self->totalScore = $totalScore;
        $self->loggedInAt = $loggedInAt;

        return $self;
    }
}
