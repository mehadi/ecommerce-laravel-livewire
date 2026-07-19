<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Per-tenant snapshot of all of this tenant's own settings, memoized for
     * the request and shared via cache. This tiny table is otherwise read
     * dozens of times per storefront render (once per Setting::get() call),
     * each a separate query.
     *
     * @var array<int|string, array<string, ?string>>
     */
    protected static array $memo = [];

    /**
     * Get a setting value by key. Falls back to the platform-wide default
     * (set by Platform Admin under Website Defaults) when the tenant hasn't
     * configured their own value, before falling back to $default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::allCached()[$key] ?? PlatformSetting::get($key) ?? $default;
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
     * Get multiple settings at once. Same platform-default fallback as get().
     */
    public static function getMany(array $keys): array
    {
        $settings = static::getManyOwn($keys);
        $platformDefaults = PlatformSetting::getMany($keys);

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? $platformDefaults[$key] ?? null;
        }

        return $result;
    }

    /**
     * Get the tenant's own raw value, ignoring the platform default fallback.
     * Used by admin edit forms so an inherited default isn't mistaken for—and
     * silently saved as—an explicit tenant override.
     */
    public static function getOwn(string $key, ?string $default = null): ?string
    {
        return static::allCached()[$key] ?? $default;
    }

    /**
     * Get multiple tenant-own raw values at once. See getOwn().
     */
    public static function getManyOwn(array $keys): array
    {
        $settings = static::allCached();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? null;
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
        $tenantKey = Tenancy::id() ?? 'none';

        return static::$memo[$tenantKey] ??= Cache::remember(
            Tenancy::cacheKey('settings.all'),
            3600,
            fn () => static::pluck('value', 'key')->all()
        );
    }

    protected static function forgetCache(): void
    {
        unset(static::$memo[Tenancy::id() ?? 'none']);
        Cache::forget(Tenancy::cacheKey('settings.all'));
    }

    /**
     * Reset the in-process memo for every tenant. Tests need this because the
     * static array otherwise outlives each test's DB transaction rollback
     * (RefreshDatabase resets the database, not this in-memory array).
     */
    public static function flushCache(): void
    {
        static::$memo = [];
    }
}
