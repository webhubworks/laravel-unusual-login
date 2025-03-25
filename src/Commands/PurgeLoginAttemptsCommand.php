<?php

namespace WebhubWorks\UnusualLogin\Commands;

use Illuminate\Console\Command;
use WebhubWorks\UnusualLogin\Concerns\WorksWithLoginAttempts;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;

class PurgeLoginAttemptsCommand extends Command
{
    use WorksWithLoginAttempts;

    public $signature = 'unusual-login:purge-login-attempts';

    public $description = 'Purges all UserLoginAttempt entries older than the configured minutes.';

    public function handle(): int
    {
        $resetLoginAttemptsAfterMinutes = $this->getResetLoginAttemptsAfterMinutes();

        $loginAttempts = UserLoginAttempt::query()
            ->where('updated_at', '<', now()->subMinutes($resetLoginAttemptsAfterMinutes))
            ->get();

        if($loginAttempts->isEmpty()) {
            $this->info('No UserLoginAttempt entries to purge.');

            return self::SUCCESS;
        }

        $this->info("Purging ".$loginAttempts->count()." UserLoginAttempt entries...");

        $loginAttempts->each->delete();

        return self::SUCCESS;
    }
}
