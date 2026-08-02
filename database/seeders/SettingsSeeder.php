<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

final class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'platform_name', 'value' => 'TotalCashPro', 'type' => 'string'],
            ['group' => 'general', 'key' => 'support_email', 'value' => 'hello@totalcashpro.com', 'type' => 'string'],
            ['group' => 'general', 'key' => 'default_currency', 'value' => 'GBP', 'type' => 'string'],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Europe/London', 'type' => 'string'],
            ['group' => 'brand', 'key' => 'primary_color', 'value' => '#16A34A', 'type' => 'string'],
            ['group' => 'brand', 'key' => 'logo_path', 'value' => '/logo.png', 'type' => 'string'],
            ['group' => 'brand', 'key' => 'favicon_path', 'value' => '/logo.png', 'type' => 'string'],
            ['group' => 'seo', 'key' => 'default_title', 'value' => 'TotalCashPro', 'type' => 'string'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Cash, staff and reporting for multi-branch businesses.', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_name', 'value' => 'TotalCashPro', 'type' => 'string'],
            ['group' => 'email', 'key' => 'from_address', 'value' => 'noreply@totalcashpro.com', 'type' => 'string'],
            ['group' => 'payments', 'key' => 'provider', 'value' => 'manual', 'type' => 'string'],
            ['group' => 'payments', 'key' => 'currency', 'value' => 'GBP', 'type' => 'string'],
            ['group' => 'system', 'key' => 'app_environment', 'value' => 'production', 'type' => 'string'],
            ['group' => 'system', 'key' => 'queue_driver', 'value' => 'database', 'type' => 'string'],
            ['group' => 'appearance', 'key' => 'default_theme', 'value' => 'Light', 'type' => 'string'],
            ['group' => 'appearance', 'key' => 'density', 'value' => 'Comfortable', 'type' => 'string'],
            ['group' => 'maintenance', 'key' => 'maintenance_mode', 'value' => 'Off', 'type' => 'string'],
            ['group' => 'maintenance', 'key' => 'banner_message', 'value' => 'Scheduled maintenance window', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'locale', 'value' => 'en_GB', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'date_format', 'value' => 'd M Y', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'currency', 'value' => 'GBP', 'type' => 'string'],
            ['group' => 'localization', 'key' => 'timezone', 'value' => 'Europe/London', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']],
            );
        }
    }
}
