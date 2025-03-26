<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pipeline\Pipeline;
use InvalidArgumentException;
use WebhubWorks\UnusualLogin\Concerns\WorksWithLoginAttempts;
use WebhubWorks\UnusualLogin\DTOs\CheckData;
use WebhubWorks\UnusualLogin\Events\UnusualLoginDetected;
use WebhubWorks\UnusualLogin\Models\UserLogin;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;
use WebhubWorks\UnusualLogin\UnusualLogin;

class LoginListener
{
    use WorksWithLoginAttempts;

    /**
     * @throws Exception
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $loginAttempts = $this->getLoginAttempts($user);

        $currentIpAddress = request()->ip();
        $currentUserAgent = request()->userAgent();

        $lastUserLogin = UserLogin::where('user_id', $user->id)->latest()->first();

        if(! $lastUserLogin) {
            $lastUserLogin = UserLogin::create([
                'user_id' => $user->id,
                'ip_address' => $currentIpAddress,
                'user_agent' => $currentUserAgent,
            ]);
        }

        $checks = UnusualLogin::getChecks()->toArray();
        $threshold = UnusualLogin::getThreshold();

        /** @var CheckData $checkData */
        $checkData = app(Pipeline::class)
            ->send(CheckData::make(
                user: $user,
                currentIpAddress: $currentIpAddress,
                currentUserAgent: $currentUserAgent,
                lastUserLogin: $lastUserLogin,
                loginAttempts: $loginAttempts,
                totalScore: 0,
                loggedInAt: now(),
            ))
            ->through($checks)
            ->thenReturn();

        if($checkData->totalScore >= $threshold) {
            UnusualLoginDetected::dispatch($checkData);

            $this->handleNotification($user, $checkData);
        }

        $this->resetUserLoginAttempts($user);
        $this->cleanUp($user, $lastUserLogin, $currentIpAddress, $currentUserAgent);
    }

    /**
     * @throws Exception
     */
    private function handleNotification(Authenticatable $user, CheckData $checkData): void
    {
        if(! in_array(Notifiable::class, class_uses($user))){
            return;
        }

        $notificationClass = config('unusual-login.notification');
        if(! $notificationClass){
            return;
        }

        if (! class_exists($notificationClass)) {
            throw new Exception("Notification class does not exist: {$notificationClass}");
        }

        /** @var Notifiable $user */
        $user->notify(new $notificationClass($checkData));
    }

    private function cleanUp(Authenticatable $user, $lastUserLogin, ?string $currentIpAddress, ?string $currentUserAgent): void
    {
        UserLogin::where('user_id', $user->id)
            ->where('id', '<>', $lastUserLogin->id)
            ->delete();

        $lastUserLogin->update([
            'ip_address' => $currentIpAddress,
            'user_agent' => $currentUserAgent,
        ]);
    }

    private function resetUserLoginAttempts(Authenticatable $user): void
    {
        $identifier = $this->getUserIdentifiesVia();
        if(! $identifier) {
            return;
        }

        UserLoginAttempt::query()
            ->where('identifier', $user->{$identifier})
            ->each(function (UserLoginAttempt $attempt) use ($user) {
                $attempt->delete();
            });
    }

    private function getLoginAttempts(Authenticatable $user): int
    {
        $identifier = $this->getUserIdentifiesVia();
        if(! $identifier) {
            return 0;
        }

        return UserLoginAttempt::query()
            ->where('identifier', $user->{$identifier})
            ->first()?->attempts ?? 0;
    }
}
