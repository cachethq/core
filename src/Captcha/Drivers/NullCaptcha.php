<?php

namespace Cachet\Captcha\Drivers;

use Cachet\Captcha\Captcha;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

class NullCaptcha implements Captcha
{
    public function enabled(): bool
    {
        return false;
    }

    public function render(): Htmlable
    {
        return new HtmlString('');
    }

    public function verify(Request $request): bool
    {
        return true;
    }
}
