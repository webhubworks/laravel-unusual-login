<?php

namespace WebhubWorks\UnusualLogin\Commands;

use Illuminate\Console\Command;

class UnusualLoginCommand extends Command
{
    public $signature = 'laravel-unusual-login';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
