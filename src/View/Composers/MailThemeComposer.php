<?php

namespace Cachet\View\Composers;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\AppSettings;
use Cachet\Settings\ThemeSettings;
use Illuminate\View\View;

class MailThemeComposer
{
    public function __construct(
        private readonly AppSettings $appSettings,
        private readonly ThemeSettings $themeSettings,
    ) {}

    /**
     * Provide the status page theme to every Cachet mail view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'appName' => $this->appSettings->name ?? config('cachet.title'),
            'appBanner' => $this->themeSettings->app_banner,
            'colors' => array_map(
                static::hex(...),
                (new ThemeData($this->themeSettings))->lightColors(),
            ),
        ]);
    }

    /**
     * Convert an oklch() CSS color to a hex color that email clients understand.
     *
     * Colors in any other notation are returned untouched.
     */
    public static function hex(string $color): string
    {
        if (! preg_match('/^oklch\(\s*([\d.]+%?)\s+([\d.]+)\s+([\d.]+)\s*(?:\/.*)?\)$/i', trim($color), $matches)) {
            return $color;
        }

        $lightness = str_ends_with($matches[1], '%') ? ((float) $matches[1]) / 100 : (float) $matches[1];
        $chroma = (float) $matches[2];
        $hue = deg2rad((float) $matches[3]);

        $a = $chroma * cos($hue);
        $b = $chroma * sin($hue);

        $l = ($lightness + 0.3963377774 * $a + 0.2158037573 * $b) ** 3;
        $m = ($lightness - 0.1055613458 * $a - 0.0638541728 * $b) ** 3;
        $s = ($lightness - 0.0894841775 * $a - 1.2914855480 * $b) ** 3;

        $channels = [
            4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
            -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
            -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
        ];

        return '#'.implode('', array_map(function (float $channel): string {
            $srgb = $channel <= 0.0031308
                ? 12.92 * $channel
                : 1.055 * ($channel ** (1 / 2.4)) - 0.055;

            return str_pad(dechex((int) round(min(max($srgb, 0), 1) * 255)), 2, '0', STR_PAD_LEFT);
        }, $channels));
    }
}
