<?php

use Cachet\Enums\ThemeModeEnum;
use Cachet\Settings\ThemeSettings;

it('shows the theme toggle and follows the system preference by default', function () {
    visit(route('cachet.status-page'))
        ->inLightMode()
        ->assertScript('document.documentElement.dataset.themeMode', 'auto')
        ->assertVisible('[data-theme-toggle]')
        ->assertScript("document.documentElement.classList.contains('light')")
        ->assertScript("document.documentElement.classList.contains('dark')", false)
        ->assertNoJavaScriptErrors();
});

it('resolves to dark for visitors whose system prefers dark', function () {
    visit(route('cachet.status-page'))
        ->inDarkMode()
        ->assertScript("document.documentElement.classList.contains('dark')")
        ->assertScript('getComputedStyle(document.documentElement).colorScheme', 'dark');
});

it('lets visitors pick a theme and persists the choice across reloads', function () {
    visit(route('cachet.status-page'))
        ->inLightMode()
        ->click('[data-theme-toggle] button[title="Dark"]')
        ->assertScript("document.documentElement.classList.contains('dark')")
        ->assertScript("localStorage.getItem('cachet.theme')", 'dark')
        ->refresh()
        ->assertScript("document.documentElement.classList.contains('dark')")
        ->assertAriaAttribute('[data-theme-toggle] button[title="Dark"]', 'checked', 'true');
});

it('returns to the system preference when visitors pick the system theme', function () {
    visit(route('cachet.status-page'))
        ->inLightMode()
        ->click('[data-theme-toggle] button[title="Dark"]')
        ->assertScript("document.documentElement.classList.contains('dark')")
        ->click('[data-theme-toggle] button[title="System"]')
        ->assertScript("document.documentElement.classList.contains('light')")
        ->assertScript("localStorage.getItem('cachet.theme')", 'auto');
});

it('falls back to the system preference when the stored theme is invalid', function () {
    $page = visit(route('cachet.status-page'))->inLightMode();

    $page->script("localStorage.setItem('cachet.theme', 'purple')");

    $page->refresh()
        ->assertScript("document.documentElement.classList.contains('light')")
        ->assertAriaAttribute('[data-theme-toggle] button[title="System"]', 'checked', 'true');
});

it('forces the theme and hides the theme toggle when a theme mode is forced', function (ThemeModeEnum $mode) {
    $settings = app(ThemeSettings::class);
    $settings->theme_mode = $mode;
    $settings->save();

    $page = visit(route('cachet.status-page'));

    $page = $mode === ThemeModeEnum::dark ? $page->inLightMode() : $page->inDarkMode();

    $page->assertScript('document.documentElement.dataset.themeMode', $mode->value)
        ->assertNotPresent('[data-theme-toggle]')
        ->assertScript("document.documentElement.classList.contains('{$mode->value}')")
        ->assertScript('getComputedStyle(document.documentElement).colorScheme', $mode->value);
})->with([ThemeModeEnum::light, ThemeModeEnum::dark]);
