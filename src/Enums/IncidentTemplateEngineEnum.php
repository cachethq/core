<?php

namespace Cachet\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IncidentTemplateEngineEnum: string implements HasColor, HasIcon, HasLabel
{
    case twig = 'twig';

    public function getColor(): array
    {
        return match ($this) {
            self::twig => Color::Zinc,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::twig => __('cachet::incident_template.engine.twig'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::twig => 'cachet-twig',
        };
    }
}
