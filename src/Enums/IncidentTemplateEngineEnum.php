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

    /**
     * The engines whose renderer is available to a template.
     *
     * @return list<self>
     */
    public static function available(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $engine): bool => $engine->isAvailable(),
        ));
    }

    /**
     * Determine if the engine may be selected for a template.
     *
     * Blade template bodies compile to PHP, so the Blade renderer is only
     * available when an installation enables cachet.renderers.blade.
     */
    public function isAvailable(): bool
    {
        return match ($this) {
            self::blade => (bool) config('cachet.renderers.blade'),
            self::twig => true,
        };
    }

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
