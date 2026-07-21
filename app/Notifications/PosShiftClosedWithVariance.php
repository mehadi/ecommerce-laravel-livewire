<?php

namespace App\Notifications;

use App\Models\PosShift;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a tenant's manager/admin/super-admin staff (not platform staff —
 * this is a tenant-internal operational alert) whenever a POS shift closes
 * with a non-zero cash variance. Follows the existing plain (non-queued)
 * Notification pattern and links back via the tenant's own primaryUrl(),
 * matching TrialEndingSoon's staff-facing-link convention rather than
 * Tenancy::platformUrl() (which is reserved for platform-staff notices).
 */
class PosShiftClosedWithVariance extends Notification
{
    use Queueable;

    public function __construct(protected PosShift $shift, protected Tenant $tenant) {}

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
            ->subject(__('POS shift closed with a cash variance'))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('A POS shift on register ":register" closed with a variance of :variance.', [
                'register' => $this->shift->register?->name,
                'variance' => number_format((float) $this->shift->variance, 2),
            ]))
            ->action(__('Review POS shifts'), $this->tenant->primaryUrl().'/admin/pos/shifts');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('POS shift variance'),
            'body' => __('Register ":register" closed with a variance of :variance.', [
                'register' => $this->shift->register?->name,
                'variance' => number_format((float) $this->shift->variance, 2),
            ]),
            'url' => $this->tenant->primaryUrl().'/admin/pos/shifts',
        ];
    }
}
