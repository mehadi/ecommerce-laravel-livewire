<?php

namespace App\Notifications;

use App\Models\Tenant;
use App\Models\WastageLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a tenant's manager/admin/super-admin staff (not platform staff —
 * this is a tenant-internal operational alert) whenever a wastage report is
 * filed and awaits approval. Follows the same plain (non-queued) Notification
 * pattern and tenant primaryUrl() link convention as PosShiftClosedWithVariance.
 */
class WastageReportedForApproval extends Notification
{
    use Queueable;

    public function __construct(protected WastageLog $log, protected Tenant $tenant) {}

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
            ->subject(__('Wastage report awaiting approval'))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('A wastage report for :product (qty :quantity, reason: :reason) needs your review.', [
                'product' => $this->log->product?->name_en,
                'quantity' => $this->log->quantity,
                'reason' => $this->log->reason->label(),
            ]))
            ->action(__('Review wastage reports'), $this->tenant->primaryUrl().'/admin/wastage');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Wastage report awaiting approval'),
            'body' => __(':product (qty :quantity) reported as :reason.', [
                'product' => $this->log->product?->name_en,
                'quantity' => $this->log->quantity,
                'reason' => $this->log->reason->label(),
            ]),
            'url' => $this->tenant->primaryUrl().'/admin/wastage',
        ];
    }
}
