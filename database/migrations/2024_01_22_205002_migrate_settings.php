<?php

use Cachet\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If there are no settings, then there's nothing to migrate.
        if (Setting::count() === 0) {
            return;
        }

        Setting::query()->chunk(10, function (Collection $settings) {
            $settings->each(function (Setting $setting) {
                $setting->update([
                    'name' => $this->settingName($setting->name),
                    'group' => $this->settingGroup($setting->name),
                    'value' => json_encode($setting->value),
                ]);
            });
        });
    }

    private function settingName(string $name): string
    {
        return match (true) {
            Str::startsWith($name, ['app_']) => Str::replaceFirst('app_', 'setting_', $name),
            Str::startsWith($name, ['style_']) => Str::replaceFirst('style_', '', $name),
            default => $name,
        };
    }

    private function settingGroup(string $name): string
    {
        return match (true) {
            Str::startsWith($name, ['app_', 'show_support', 'display_graphs', 'enable_subscribers', 'dashboard_']) => 'cachet',
            Str::contains($name, ['header', 'footer', 'stylesheet']) => 'customization',
            Str::startsWith($name, ['style_']) => 'theme',
            Str::contains($name, ['date_format', 'automatic_localization']) => 'localization',
            Str::contains($name, ['always_authenticate', 'allowed_domains']) => 'security',
            default => 'cachet',
        };
    }
};
