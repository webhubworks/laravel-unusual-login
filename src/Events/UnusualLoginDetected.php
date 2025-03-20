<?php

namespace WebhubWorks\UnusualLogin\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UnusualLoginDetected
{
    use SerializesModels;
    use Dispatchable;

    public function __construct(
        public Model $user,
    ) {
        //
    }
}
