<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Http\Controllers\RssController;
use Cachet\Settings\RssSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class ManageRss extends SettingsPage
{
    protected static string $settings = RssSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_rss');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('cachet::settings.manage_rss.feed_section_title'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_items')
                            ->label(__('cachet::settings.manage_rss.max_items_label'))
                            ->helperText(__('cachet::settings.manage_rss.max_items_helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(500)
                            ->required(),
                        TextInput::make('refresh_interval')
                            ->label(__('cachet::settings.manage_rss.refresh_interval_label'))
                            ->helperText(__('cachet::settings.manage_rss.refresh_interval_helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(10)
                            ->maxValue(86400)
                            ->suffix(__('cachet::settings.manage_rss.refresh_interval_suffix'))
                            ->required(),
                    ]),

                Section::make(__('cachet::settings.manage_rss.incidents_section_title'))
                    ->description(__('cachet::settings.manage_rss.incidents_section_description'))
                    ->columns(2)
                    ->schema(array_map(
                        fn (IncidentStatusEnum $status) => Toggle::make('incident_'.$status->name)->label($status->getLabel()),
                        IncidentStatusEnum::cases(),
                    )),

                Section::make(__('cachet::settings.manage_rss.maintenance_section_title'))
                    ->description(__('cachet::settings.manage_rss.maintenance_section_description'))
                    ->columns(2)
                    ->schema(array_map(
                        fn (ScheduleStatusEnum $status) => Toggle::make('maintenance_'.$status->name)->label($status->getLabel()),
                        ScheduleStatusEnum::cases(),
                    )),
            ]);
    }

    /**
     * Drop the cached feeds so the new settings apply immediately.
     */
    protected function afterSave(): void
    {
        foreach ([RssController::CACHE_KEY_COMBINED, RssController::CACHE_KEY_INCIDENTS, RssController::CACHE_KEY_MAINTENANCE] as $key) {
            Cache::forget($key);
            Cache::forget($key.'-last-modified');
        }
    }
}
