<?php

namespace WebhubWorks\UnusualLogin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WebhubWorks\UnusualLogin\UnusualLogin
 */
class UnusualLogin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \WebhubWorks\UnusualLogin\UnusualLogin::class;
    }
}
