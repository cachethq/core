<?php

namespace Cachet\Captcha\Drivers;

/**
 * Cloudflare Turnstile.
 */
class TurnstileCaptcha extends SiteVerifyCaptcha
{
    protected function view(): string
    {
        return 'cachet::captcha.turnstile';
    }

    protected function responseField(): string
    {
        return 'cf-turnstile-response';
    }

    protected function verifyUrl(): string
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    }
}
