<?php

namespace Cachet\Filament\Pages;

use Cachet\Filament\Schemas\Components\Section as CachetSection;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    protected Width|string|null $maxContentWidth = Width::SixExtraLarge;

    public function getTitle(): string|Htmlable
    {
        return __('cachet::navigation.user.items.edit_profile');
    }

    public static function isSimple(): bool
    {
        return false;
    }

    public function getMultiFactorAuthenticationContentComponent(): ?Component
    {
        $component = parent::getMultiFactorAuthenticationContentComponent();

        if (! $component instanceof Section) {
            return $component;
        }

        return $component
            ->label(null)
            ->heading(__('filament-panels::auth/pages/edit-profile.multi_factor_authentication.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel(false)
            ->components([
                CachetSection::make(__('cachet::user.profile_information_title'))
                    ->columns(2)
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ]),

                CachetSection::make(__('cachet::user.security_section_title'))
                    ->columns(2)
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ]),
            ]);
    }
}
