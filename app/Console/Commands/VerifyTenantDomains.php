<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;

class VerifyTenantDomains extends Command
{
    protected $signature = 'tenants:verify-domains';

    protected $description = 'Check DNS for pending tenant custom domains and mark the ones that point at this platform as verified.';

    public function handle(): int
    {
        $target = config('tenancy.domain_verification_target');

        if (! $target) {
            $this->error('config(tenancy.domain_verification_target) / TENANT_DOMAIN_TARGET is not set.');

            return self::FAILURE;
        }

        $pending = Domain::whereNull('verified_at')->get();

        if ($pending->isEmpty()) {
            $this->info('No pending domains to verify.');

            return self::SUCCESS;
        }

        foreach ($pending as $domain) {
            if ($this->pointsAtPlatform($domain->domain, $target)) {
                $domain->update(['verified_at' => now()]);
                $this->info("Verified: {$domain->domain}");
            } else {
                $this->line("Not yet verified: {$domain->domain}");
            }
        }

        return self::SUCCESS;
    }

    public function pointsAtPlatform(string $domain, string $target): bool
    {
        $cnames = @dns_get_record($domain, DNS_CNAME) ?: [];

        foreach ($cnames as $record) {
            if (rtrim($record['target'] ?? '', '.') === rtrim($target, '.')) {
                return true;
            }
        }

        $targetIps = array_column(@dns_get_record($target, DNS_A) ?: [], 'ip');
        $domainIps = array_column(@dns_get_record($domain, DNS_A) ?: [], 'ip');

        return $targetIps !== [] && array_intersect($targetIps, $domainIps) !== [];
    }
}
