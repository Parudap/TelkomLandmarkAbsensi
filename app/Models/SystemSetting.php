<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $primaryKey = 'setting_key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
        'category',
        'type',
        'is_editable',
    ];

    protected function casts(): array
    {
        return [
            'is_editable' => 'boolean',
        ];
    }

    // Helper method untuk get setting dengan cache
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::find($key);
            return $setting ? $setting->setting_value : $default;
        });
    }

    // Helper method untuk set setting
    public static function set(string $key, $value): bool
    {
        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
        
        Cache::forget("setting_{$key}");
        return true;
    }

    // Clear all settings cache
    public static function clearCache(): void
    {
        Cache::flush();
    }

    // Scope untuk filter by category
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }
}
