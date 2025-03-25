<?php

namespace WebhubWorks\UnusualLogin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jenssegers\Agent\Agent;
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
        $userAgent = new Agent();
        $userAgent->setUserAgent($this->checkData->currentUserAgent);

        $userAgentData = [
            __('Platform').': '.$userAgent->platform(),
            __('Browser').': '.$userAgent->browser(),
            __('Version').': '.$userAgent->version($userAgent->browser()),
            __('Timezone').': '.$this->checkData->loggedInAt->format("Y-m-d H:i:s T"),
        ];

        return (new MailMessage)
            ->subject(__('Unusual login detected.'))
            ->line(__('we have detected an unusual login attempt on your account:'))
            ->line('**' . implode(", ", $userAgentData) . '**')
            ->line(__('If this was you, you can safely ignore this email.'));
    }
}
