<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'owner_user_id',
        'plan_id',
        'desired_plan_id',
        'status',
        'trial_ends_at',
        'trial_ending_notified_at',
        'upgrade_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'trial_ending_notified_at' => 'datetime',
            'upgrade_requested_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            if ($tenant->plan_id === null) {
                $tenant->plan_id = Plan::where('is_default', true)->value('id');
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function desiredPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'desired_plan_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function billingEvents(): HasMany
    {
        return $this->hasMany(TenantBillingEvent::class)->latest();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasFeature(string $key): bool
    {
        return $this->plan?->hasFeature($key) ?? false;
    }

    /**
     * An absolute URL to this tenant's own domain (verified custom domain if it
     * has one, else its free subdomain) — used for cross-host redirects such as
     * starting tenant impersonation from the central Platform domain.
     */
    public function primaryUrl(): string
    {
        $scheme = request()->getScheme();
        $port = request()->getPort();
        $portSuffix = in_array($port, [80, 443], true) ? '' : ':'.$port;

        $domain = $this->domains()->whereNotNull('verified_at')->value('domain');

        if ($domain) {
            return $scheme.'://'.$domain.$portSuffix;
        }

        $central = config('tenancy.central_domains')[0] ?? request()->getHost();

        return $scheme.'://'.$this->slug.'.'.$central.$portSuffix;
    }

    public function canAddProduct(): bool
    {
        return $this->withinLimit('max_products', fn () => $this->products()->count());
    }

    public function canAddAdminUser(): bool
    {
        return $this->withinLimit('max_admin_users', fn () => User::where('tenant_id', $this->id)->count());
    }

    public function canAddCustomDomain(): bool
    {
        return $this->withinLimit('max_custom_domains', fn () => $this->domains()->count());
    }

    protected function withinLimit(string $limitColumn, \Closure $currentCount): bool
    {
        $limit = $this->plan?->{$limitColumn};

        if ($limit === null) {
            return true;
        }

        return $currentCount() < $limit;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
