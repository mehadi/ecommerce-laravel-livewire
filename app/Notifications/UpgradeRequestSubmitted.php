<?php

namespace App\Notifications;

use App\Models\Tenant;
use App\Support\Tenancy;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpgradeRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(protected Tenant $tenant) {}

    /**
     * route('platform.tenants.show', ...) would generate an absolute URL using
     * whatever host triggered this notification (often the TENANT's own domain,
     * since requestUpgrade() is a tenant-side action) — platform staff must
     * always be linked to the actual central/platform domain instead.
     */
    protected function url(): string
    {
        return Tenancy::platformUrl('/platform/tenants/'.$this->tenant->id);
    }

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
            ->subject(__('Upgrade request: :store', ['store' => $this->tenant->name]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__(':store has requested to move from :current to :desired.', [
                'store' => $this->tenant->name,
                'current' => $this->tenant->plan?->name ?? __('no plan'),
                'desired' => $this->tenant->desiredPlan?->name ?? __('no plan'),
            ]))
            ->action(__('Review request'), $this->url());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('New upgrade request'),
            'body' => __(':store wants to move to :desired.', [
                'store' => $this->tenant->name,
                'desired' => $this->tenant->desiredPlan?->name ?? __('a new plan'),
            ]),
            'url' => $this->url(),
        ];
    }
}
