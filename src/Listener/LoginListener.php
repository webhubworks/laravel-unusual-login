<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pipeline\Pipeline;
use Jenssegers\Agent\Agent;
use WebhubWorks\UnusualLogin\DTOs\CheckData;
use WebhubWorks\UnusualLogin\Events\UnusualLoginDetected;
use WebhubWorks\UnusualLogin\Models\UserLogin;
use WebhubWorks\UnusualLogin\Models\UserLoginAttempt;

class LoginListener
{
    /**
     * @throws Exception
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $loginAttempts = UserLoginAttempt::query()
            ->where('identifier', $user->{config('unusual-login.login_attempts.user_identifies_via')})
            ->first()?->attempts ?? 0;

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

        $checks = config('unusual-login.checks');
        $threshold = config('unusual-login.threshold');

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
        UserLoginAttempt::query()
            ->where('identifier', $user->{config('unusual-login.login_attempts.user_identifies_via')})
            ->each(function (UserLoginAttempt $attempt) use ($user) {
                $attempt->delete();
            });
    }
}
