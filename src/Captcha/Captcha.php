<?php

namespace Cachet\Captcha;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;

interface Captcha
{
    /**
     * Determine whether the captcha protects forms.
     */
    public function enabled(): bool;

    /**
     * Render the widget and its scripts inside a form.
     */
    public function render(): Htmlable;

    /**
     * Verify the captcha response submitted with the request.
     */
    public function verify(Request $request): bool;
}
