<?php

namespace WebhubWorks\UnusualLogin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use WebhubWorks\UnusualLogin\DTOs\CheckData;

class UnusualLoginDetected
{
    use SerializesModels;
    use Dispatchable;

    public function __construct(
        public CheckData $checkData,
    ) {
        //
    }
}
