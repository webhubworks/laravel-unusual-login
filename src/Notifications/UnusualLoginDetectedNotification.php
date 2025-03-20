<?php

namespace WebhubWorks\UnusualLogin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnusualLoginDetectedNotification extends Notification
{
    public function __construct(
        public string $platform,
        public string $browser,
        public string $version,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $userAgentData = [
            __('Platform').': '.$this->platform,
            __('Browser').': '.$this->browser,
            __('Version').': '.$this->version,
        ];

        return (new MailMessage)
            ->subject(__('Unusual login detected.'))
            ->line(__('we have detected an unusual login attempt on your account:'))
            ->line('**' . implode(", ", $userAgentData) . '**')
            ->line(__('If this was you, you can safely ignore this email.'));
    }
}
