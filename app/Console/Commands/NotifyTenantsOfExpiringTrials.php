<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Notifications\TrialEndingSoon;
use Illuminate\Console\Command;

class NotifyTenantsOfExpiringTrials extends Command
{
    protected $signature = 'platform:notify-trial-ending';

    protected $description = 'Notify tenant owners whose trial ends within the next 3 days (once per trial period).';

    public function handle(): int
    {
        $tenants = Tenant::where('status', 'active')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(3)])
            ->whereNull('trial_ending_notified_at')
            ->get();

        if ($tenants->isEmpty()) {
            $this->info('No tenants with a trial ending soon.');

            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $tenant->owner?->notify(new TrialEndingSoon($tenant));
            $tenant->update(['trial_ending_notified_at' => now()]);

            $this->info("Notified {$tenant->name} (trial ends {$tenant->trial_ends_at->toDateString()}).");
        }

        return self::SUCCESS;
    }
}
