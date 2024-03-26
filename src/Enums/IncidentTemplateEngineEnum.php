<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IncidentTemplateEngineEnum: string implements HasIcon, HasLabel
{
    case blade = 'blade';
    case twig = 'twig';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::blade => __('Laravel Blade'),
            self::twig => __('Twig'),
        };
    }

    public function getIcon(): ?string
    {
        // TODO: Implement getIcon() method.
    }
}
