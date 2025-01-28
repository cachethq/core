<?php

namespace Cachet\Filament\Pages\Settings;

abstract class SettingsPage extends \Filament\Pages\SettingsPage
{
    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }
}
