<?php

namespace WebhubWorks\UnusualLogin\Listener;

use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Agent\Agent;
use WebhubWorks\UnusualLogin\Events\UnusualLoginDetected;
use WebhubWorks\UnusualLogin\Models\UserLogin;
use WebhubWorks\UnusualLogin\Notifications\UnusualLoginDetectedNotification;

class LoginListener
{
    /**
     * @throws Exception
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        $currentIpAddress = request()->ip();
        $currentUserAgent = request()->userAgent();

        $lastUserLogin = UserLogin::where('user_id', $user->id)->latest()->first();

        if(! $lastUserLogin) {
            UserLogin::create([
                'user_id' => $user->id,
                'ip_address' => $currentIpAddress,
                'user_agent' => $currentUserAgent,
            ]);

            return;
        }

        if(
            $currentIpAddress !== $lastUserLogin->ip_address
            || $currentUserAgent !== $lastUserLogin->user_agent
        ) {
            UnusualLoginDetected::dispatch($user);

            if(in_array(Notifiable::class, class_uses($user))){

                $userAgent = new Agent();
                $userAgent->setUserAgent($currentUserAgent);

                $notificationClass = config('unusual-login.unusual-login-detected-notification');
                if (! class_exists($notificationClass)) {
                    throw new Exception("Notification class does not exist: {$notificationClass}");
                }

                /** @var Notifiable $user */
                $user->notify(new $notificationClass(
                    $userAgent->platform(),
                    $userAgent->browser(),
                    $userAgent->version($userAgent->browser()),
                ));
            }
        }

        UserLogin::where('user_id', $user->id)
            ->where('id', '<>', $lastUserLogin->id)
            ->delete();
        $lastUserLogin->update([
            'ip_address' => $currentIpAddress,
            'user_agent' => $currentUserAgent,
        ]);
    }
}
