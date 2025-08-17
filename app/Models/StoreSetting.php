<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreSetting extends Model
{
    use HasFactory;

    protected $table = 'store_settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description'
    ];

    /**
     * Get setting value by key with caching
     */
    public static function get(string $key, $default = null)
    {
        $value = Cache::remember("store_setting_{$key}", 3600, function () use ($key) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : null;
        });
        
        if ($value === null) {
            return $default;
        }

        // Get type for casting
        $setting = self::where('key', $key)->first();
        $type = $setting ? $setting->type : 'string';

        return self::castValue($value, $type);
    }

    /**
     * Set setting value by key
     */
    public static function set(string $key, $value, string $type = 'string'): bool
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type
            ]
        );

        // Clear cache
        Cache::forget("store_setting_{$key}");
        Cache::forget('all_store_settings');

        // Return true if setting exists (either created or updated)
        return $setting !== null;
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue($value, string $type)
    {
        switch ($type) {
            case 'number':
                return is_numeric($value) ? (float) $value : 0;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAll(): array
    {
        return Cache::remember('all_store_settings', 3600, function () {
            $settings = self::all();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = self::castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('all_store_settings');
        
        $keys = self::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("store_setting_{$key}");
        }
    }

    /**
     * Boot method to clear cache when model is updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}