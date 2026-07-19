<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEndingSoon extends Notification
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

    protected function daysRemaining(): int
    {
        $endsAt = $this->tenant->trial_ends_at?->copy()->startOfDay();

        return (int) max(0, now()->startOfDay()->diffInDays($endsAt, false));
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = $this->daysRemaining();

        return (new MailMessage)
            ->subject(__('Your :app trial ends in :days day(s)', ['app' => config('app.name'), 'days' => $days]))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Your trial for **:store** ends in :days day(s), on :date.', [
                'store' => $this->tenant->name,
                'days' => $days,
                'date' => $this->tenant->trial_ends_at?->toFormattedDateString(),
            ]))
            ->line(__('Upgrade to a paid plan to keep your store running without interruption.'))
            ->action(__('View billing & plans'), $this->tenant->primaryUrl().'/admin/billing');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Trial ending soon'),
            'body' => __(':store\'s trial ends in :days day(s).', ['store' => $this->tenant->name, 'days' => $this->daysRemaining()]),
            'url' => $this->tenant->primaryUrl().'/admin/billing',
        ];
    }
}
