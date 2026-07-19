<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpgradeRequestRejected extends Notification
{
    use Queueable;

    public function __construct(protected Tenant $tenant) {}

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
            ->subject(__('About your plan upgrade request'))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Your upgrade request for **:store** was not approved this time.', ['store' => $this->tenant->name]))
            ->line(__('Reach out to our support team if you have any questions.'))
            ->action(__('View billing'), $this->tenant->primaryUrl().'/admin/billing');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Upgrade request declined'),
            'body' => __('Your upgrade request for :store was not approved.', ['store' => $this->tenant->name]),
            'url' => $this->tenant->primaryUrl().'/admin/billing',
        ];
    }
}
