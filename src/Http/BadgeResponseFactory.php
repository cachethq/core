<?php

namespace Cachet\Http;

use Cachet\Badger\BadgeImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BadgeResponseFactory
{
    /**
     * Build a cacheable SVG response.
     */
    public function make(BadgeImage $badge): Response
    {
        return response((string) $badge, 200, [
            'Cache-Control' => 'public, max-age=60, s-maxage=60',
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
        ]);
    }

    /**
     * Return the requested style, falling back to the default for unsupported values.
     */
    public function style(Request $request): string
    {
        $style = $request->string('style', 'flat-square')->value();

        return in_array($style, ['flat-square', 'plastic-flat', 'flat', 'plastic', 'social', 'svg'], true)
            ? $style
            : 'flat-square';
    }
}
