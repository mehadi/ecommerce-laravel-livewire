<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantSuspended extends Notification
{
    use Queueable;

    public function __construct(protected Tenant $tenant, protected string $reason) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__(':store has been suspended', ['store' => $this->tenant->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Your store **:store** has been suspended and is temporarily unavailable to customers.', ['store' => $this->tenant->name]))
            ->line(__('Reason: :reason', ['reason' => $this->reason]))
            ->line(__('Contact our support team if you believe this is a mistake or would like to resolve it.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Store suspended'),
            'body' => __(':store has been suspended: :reason', ['store' => $this->tenant->name, 'reason' => $this->reason]),
            'url' => null,
        ];
    }
}
