<?php

namespace WebhubWorks\UnusualLogin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use WebhubWorks\UnusualLogin\DTOs\CheckData;

class UnusualLoginDetectedNotification extends Notification
{
    public function __construct(
        public CheckData $checkData,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $parsedUserAgent = $this->parseUserAgent($this->checkData->currentUserAgent);

        $userAgentData = [
            __('Platform').': '.$parsedUserAgent['platform'],
            __('Browser').': '.$parsedUserAgent['browser'],
            __('Version').': '.$parsedUserAgent['version'],
            __('Login attempts').': '.$this->checkData->loginAttempts,
            __('Timezone').': '.$this->checkData->loggedInAt->format("Y-m-d H:i:s T"),
        ];

        return (new MailMessage)
            ->subject(__('Unusual login detected.'))
            ->line(__('we have detected an unusual login attempt on your account:'))
            ->line('**' . implode(", ", $userAgentData) . '**')
            ->line(__('If this was you, you can safely ignore this email.'));
    }

    private function parseUserAgent(string $userAgent): array
    {
        // Browser detection
        $browser = 'Unknown';
        $version = '';

        if (preg_match('/Firefox\/(\d+(\.\d+)?)/', $userAgent, $m)) {
            $browser = 'Firefox';
            $version = $m[1];
        } elseif (preg_match('/Edg\/(\d+(\.\d+)?)/', $userAgent, $m)) {
            $browser = 'Edge';
            $version = $m[1];
        } elseif (preg_match('/Chrome\/(\d+(\.\d+)?)/', $userAgent, $m)) {
            $browser = 'Chrome';
            $version = $m[1];
        } elseif (preg_match('/Safari\/(\d+(\.\d+)?)/', $userAgent, $m)) {
            $browser = 'Safari';
            $version = $m[1];
        }

        // OS detection
        $platform = 'Unknown';
        if (str_contains($userAgent, 'Windows')) $platform = 'Windows';
        elseif (str_contains($userAgent, 'Mac OS')) $platform = 'macOS';
        elseif (str_contains($userAgent, 'Linux')) $platform = 'Linux';
        elseif (str_contains($userAgent, 'Android')) $platform = 'Android';
        elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) $platform = 'iOS';

        return compact('browser', 'version', 'platform');
    }
}
