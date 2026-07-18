<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key. Falls back to the platform-wide default
     * (set by Platform Admin under Website Defaults) when the tenant hasn't
     * configured their own value, before falling back to $default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? PlatformSetting::get($key) ?? $default;
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
        return static::where('key', $key)->first()?->value ?? $default;
    }

    /**
     * Get multiple tenant-own raw values at once. See getOwn().
     */
    public static function getManyOwn(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->pluck('value', 'key')->toArray();

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
            static::set($key, $value);
        }
    }
}
