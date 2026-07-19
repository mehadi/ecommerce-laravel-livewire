<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeTenant extends Notification
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
            ->subject(__('Welcome to :app, :store is live!', ['app' => config('app.name'), 'store' => $this->tenant->name]))
            ->greeting(__('Welcome, :name!', ['name' => $notifiable->name]))
            ->line(__('Your store **:store** has been created and is ready to customize.', ['store' => $this->tenant->name]))
            ->action(__('Go to your dashboard'), $this->tenant->primaryUrl().'/dashboard')
            ->line(__('You can add products, customize your storefront, and invite team members at any time from the admin panel.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Welcome to :app', ['app' => config('app.name')]),
            'body' => __(':store is ready to customize.', ['store' => $this->tenant->name]),
            'url' => $this->tenant->primaryUrl().'/dashboard',
        ];
    }
}
