<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Settings\BrandingSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageBranding extends SettingsPage
{
    protected static string $settings = BrandingSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return 'Branding';
    }

    public function getTitle(): string
    {
        return 'Branding & Layout';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Header')
                    ->description('Customize the status page header appearance.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('header_logo')
                            ->image()
                            ->imageEditor()
                            ->label('Header Logo')
                            ->helperText('Upload a custom logo for the header. Overrides the default site name / app banner in the status page header.')
                            ->disk('public')
                            ->columnSpanFull(),

                        TextInput::make('header_logo_height')
                            ->label('Logo Height (px)')
                            ->numeric()
                            ->minValue(16)
                            ->maxValue(120)
                            ->default(32)
                            ->suffix('px'),

                        ColorPicker::make('header_bg_color')
                            ->label('Background Color')
                            ->helperText('Leave empty to use the default theme color.'),

                        ColorPicker::make('header_text_color')
                            ->label('Text Color')
                            ->helperText('Leave empty for default text color.'),

                        Toggle::make('show_subscribe_button')
                            ->label('Show Subscribe Button')
                            ->helperText('Display the subscribe button in the header.')
                            ->default(true),

                        Toggle::make('show_dashboard_link')
                            ->label('Show Dashboard Link')
                            ->helperText('Display the admin dashboard login link.')
                            ->default(true),

                        KeyValue::make('header_links')
                            ->label('Extra Navigation Links')
                            ->keyLabel('Label')
                            ->valueLabel('URL')
                            ->helperText('Add custom links to the header navigation bar (e.g. "Documentation" → "https://docs.example.com").')
                            ->columnSpanFull(),
                    ]),

                Section::make('Footer')
                    ->description('Customize the status page footer appearance.')
                    ->columns(2)
                    ->schema([
                        ColorPicker::make('footer_bg_color')
                            ->label('Background Color')
                            ->helperText('Leave empty to use the default.'),

                        ColorPicker::make('footer_text_color')
                            ->label('Text Color')
                            ->helperText('Leave empty for default text color.'),

                        TextInput::make('footer_copyright')
                            ->label('Copyright Text')
                            ->placeholder('© 2026 My Company. All rights reserved.')
                            ->helperText('Custom copyright / legal notice shown in the footer.')
                            ->columnSpanFull(),

                        Toggle::make('show_cachet_branding')
                            ->label('Show "Powered by Cachet"')
                            ->helperText('Display the Cachet branding badge in the footer.')
                            ->default(true),

                        KeyValue::make('footer_links')
                            ->label('Footer Links')
                            ->keyLabel('Label')
                            ->valueLabel('URL')
                            ->helperText('Add custom links to the footer (e.g. "Privacy Policy" → "/privacy").')
                            ->columnSpanFull(),
                    ]),

                Section::make('General Branding')
                    ->description('Global look-and-feel overrides for the entire status page.')
                    ->columns(2)
                    ->schema([
                        ColorPicker::make('page_bg_color')
                            ->label('Page Background Color')
                            ->helperText('Override the page background color.'),

                        TextInput::make('favicon_url')
                            ->label('Favicon URL')
                            ->placeholder('/favicon.ico')
                            ->helperText('URL to a custom favicon. Supports absolute or site-relative paths.'),

                        Textarea::make('custom_css')
                            ->label('Additional Custom CSS')
                            ->rows(6)
                            ->extraAttributes(['class' => 'font-mono'])
                            ->helperText('Raw CSS injected after all other styles. Use this for advanced tweaks.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Transform data before it is saved to the database.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['header_links']) && is_array($data['header_links'])) {
            $links = [];
            foreach ($data['header_links'] as $label => $url) {
                $links[] = ['label' => $label, 'url' => $url];
            }
            $data['header_links'] = json_encode($links);
        }

        if (isset($data['footer_links']) && is_array($data['footer_links'])) {
            $links = [];
            foreach ($data['footer_links'] as $label => $url) {
                $links[] = ['label' => $label, 'url' => $url];
            }
            $data['footer_links'] = json_encode($links);
        }

        return $data;
    }

    /**
     * Transform data after it is loaded from the database.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['header_links']) && is_string($data['header_links'])) {
            $links = json_decode($data['header_links'], true) ?: [];
            $data['header_links'] = collect($links)->pluck('url', 'label')->toArray();
        } else {
            $data['header_links'] = [];
        }

        if (! empty($data['footer_links']) && is_string($data['footer_links'])) {
            $links = json_decode($data['footer_links'], true) ?: [];
            $data['footer_links'] = collect($links)->pluck('url', 'label')->toArray();
        } else {
            $data['footer_links'] = [];
        }

        return $data;
    }
}
