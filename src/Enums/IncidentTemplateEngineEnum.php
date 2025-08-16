<?php

namespace Cachet\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IncidentTemplateEngineEnum: string implements HasColor, HasIcon, HasLabel
{
    case blade = 'blade';
    case twig = 'twig';

    public function getColor(): array
    {
        return match ($this) {
            self::blade => Color::generateV3Palette('rgb(249, 50, 44)'),
            self::twig => Color::Zinc,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::blade => __('cachet::incident_template.engine.laravel_blade'),
            self::twig => __('cachet::incident_template.engine.twig'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::blade => 'cachet-laravel',
            self::twig => 'cachet-twig',
        };
    }
}
