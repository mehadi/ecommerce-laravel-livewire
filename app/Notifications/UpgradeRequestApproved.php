<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpgradeRequestApproved extends Notification
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
            ->subject(__('Your plan upgrade was approved'))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Good news — your upgrade request for **:store** has been approved. You are now on the :plan plan.', [
                'store' => $this->tenant->name,
                'plan' => $this->tenant->plan?->name ?? __('new'),
            ]))
            ->action(__('View billing'), $this->tenant->primaryUrl().'/admin/billing');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Upgrade approved'),
            'body' => __(':store is now on the :plan plan.', [
                'store' => $this->tenant->name,
                'plan' => $this->tenant->plan?->name ?? __('new'),
            ]),
            'url' => $this->tenant->primaryUrl().'/admin/billing',
        ];
    }
}
