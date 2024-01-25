<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If there are no settings, then there's nothing to migrate.
        if (DB::table('settings')->count() === 0) {
            return;
        }

        DB::table('settings')
            ->get()
            ->each(function ($setting) {
                DB::table('settings')->select('id', 'name', 'value')->where('id', $setting->id)->update([
                    'name' => $this->settingName($setting->name),
                    'group' => $this->settingGroup($setting->name),
                    'payload' => json_encode($setting->value),
                ]);
            });
    }

    private function settingName(string $name): string
    {
        return match (true) {
            Str::startsWith($name, ['app_']) => Str::replaceFirst('app_', '', $name),
            Str::startsWith($name, ['style_']) => Str::replaceFirst('style_', '', $name),
            Str::startsWith($name, ['analytics_']) => Str::replaceFirst('analytics_', '', $name),
            default => $name,
        };
    }

    private function settingGroup(string $name): string
    {
        return match (true) {
            Str::startsWith($name, ['app_', 'show_support', 'display_graphs', 'enable_subscribers', 'dashboard_']) => 'app',
            Str::contains($name, ['header', 'footer', 'stylesheet']) => 'customization',
            Str::startsWith($name, ['style_']) => 'theme',
            Str::contains($name, ['date_format', 'automatic_localization']) => 'localization',
            Str::contains($name, ['always_authenticate', 'allowed_domains']) => 'security',
            Str::contains($name, ['analytics_']) => 'analytics',
            default => 'cachet',
        };
    }
};
