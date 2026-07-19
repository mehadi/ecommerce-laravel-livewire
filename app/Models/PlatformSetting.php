<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Global (non-tenant-scoped) key-value settings store for the Platform admin
 * area. Mirrors App\Models\Setting's API exactly, but is never tenant-scoped —
 * see the create_platform_settings_table migration for why Setting itself is
 * unsafe to reuse here.
 */
class PlatformSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Memoized snapshot of every row — this table is read as the fallback
     * for nearly every Setting::get() call across a storefront render, so
     * it's worth caching even though it isn't tenant-scoped.
     *
     * @var array<string, ?string>|null
     */
    protected static ?array $memo = null;

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::allCached()[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, ?string $value = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        static::forgetCache();
    }

    /**
     * Get multiple settings at once.
     */
    public static function getMany(array $keys): array
    {
        $all = static::allCached();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $all[$key] ?? null;
        }

        return $result;
    }

    /**
     * Set multiple settings at once.
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        static::forgetCache();
    }

    /**
     * @return array<string, ?string>
     */
    protected static function allCached(): array
    {
        return static::$memo ??= Cache::remember(
            'platform_settings.all',
            3600,
            fn () => static::pluck('value', 'key')->all()
        );
    }

    protected static function forgetCache(): void
    {
        static::$memo = null;
        Cache::forget('platform_settings.all');
    }

    /**
     * Reset the in-process memo. Tests need this because the static array
     * otherwise outlives each test's DB transaction rollback (RefreshDatabase
     * resets the database, not this in-memory array).
     */
    public static function flushCache(): void
    {
        static::$memo = null;
    }
}
