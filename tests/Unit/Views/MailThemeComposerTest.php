<?php

namespace Tests\Unit\Views;

use Cachet\View\Composers\MailThemeComposer;

it('converts oklch colors to hex', function (string $oklch, string $hex) {
    expect(MailThemeComposer::hex($oklch))->toBe($hex);
})->with([
    'white' => ['oklch(1 0 0)', '#ffffff'],
    'black' => ['oklch(0 0 0)', '#000000'],
    'zinc 50' => ['oklch(0.985 0 0)', '#fafafa'],
    'percentage lightness' => ['oklch(98.5% 0 0)', '#fafafa'],
]);

it('leaves other color notations untouched', function () {
    expect(MailThemeComposer::hex('#16a34a'))->toBe('#16a34a')
        ->and(MailThemeComposer::hex('rgb(22, 163, 74)'))->toBe('rgb(22, 163, 74)');
});
