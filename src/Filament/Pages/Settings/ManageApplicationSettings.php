<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;

class ManageApplicationSettings extends Page
{
    protected static string $view = 'cachet::filament.pages.settings.manage-application-settings';

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            Setting::pluck('value', 'name')->all()
        );
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('app_name')
                ->label('App Name')
                ->placeholder('Cachet'),
        ])->columns(2)->statePath('data');
    }

    public function save()
    {
        $data = collect($this->form->getState())->map(fn ($value, $name) => [
            'value' => $value,
            'name' => $name,
        ])->all();

        // @todo Unique index.
        Setting::upsert($data, 'name');
    }
}
