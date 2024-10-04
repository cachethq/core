<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IncidentTemplateEngineEnum: string implements HasColor, HasIcon, HasLabel
{
    case blade = 'blade';
    case twig = 'twig';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::blade => 'laravel',
            self::twig => 'gray',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::blade => __('Laravel Blade'),
            self::twig => __('Twig'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::blade => 'cachet-laravel',
            self::twig => 'cachet-twig',
        };
    }
}
