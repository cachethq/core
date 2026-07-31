<?php

namespace Tests\Unit\Data;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\ThemeSettings;
use Filament\Support\Colors\Color;

/**
 * The accent select is built from Filament's palette minus the grays, so any
 * color Filament adds becomes selectable without Cachet knowing about it.
 */
function selectableAccents(): array
{
    return collect(Color::all())
        ->except(ThemeData::GRAYS)
        ->keys()
        ->push('cachet')
        ->all();
}

it('compiles styles for every selectable accent', function () {
    foreach (selectableAccents() as $accent) {
        $settings = app(ThemeSettings::class)->fill([
            'accent' => $accent,
            'accent_content' => null,
            'accent_pairing' => true,
        ]);

        expect((new ThemeData($settings))->styles)
            ->toContain('--accent:', '--accent-background:');
    }
});

it('pairs every selectable accent with a gray', function () {
    foreach (selectableAccents() as $accent) {
        expect(ThemeData::matchPairing($accent))->toBeIn(ThemeData::GRAYS);
    }
});

it('compiles styles for every selectable accent content', function () {
    foreach (ThemeData::GRAYS as $gray) {
        $settings = app(ThemeSettings::class)->fill([
            'accent' => 'cachet',
            'accent_content' => $gray,
            'accent_pairing' => false,
        ]);

        expect((new ThemeData($settings))->styles)->toContain('--accent-background:');
    }
});

it('falls back to the cachet accent when the stored accent is unknown', function () {
    $settings = app(ThemeSettings::class)->fill([
        'accent' => 'not-a-real-color',
        'accent_content' => null,
        'accent_pairing' => true,
    ]);

    expect((new ThemeData($settings))->styles)->toContain('--accent:');
});
