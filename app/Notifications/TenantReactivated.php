<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantReactivated extends Notification
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
            ->subject(__(':store is active again', ['store' => $this->tenant->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Good news — your store **:store** has been reactivated and is available to customers again.', ['store' => $this->tenant->name]))
            ->action(__('Go to your dashboard'), $this->tenant->primaryUrl().'/dashboard');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Store reactivated'),
            'body' => __(':store is active again.', ['store' => $this->tenant->name]),
            'url' => $this->tenant->primaryUrl().'/dashboard',
        ];
    }
}
