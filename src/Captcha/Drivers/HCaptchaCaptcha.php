<?php

namespace Cachet\Captcha\Drivers;

/**
 * hCaptcha.
 */
class HCaptchaCaptcha extends SiteVerifyCaptcha
{
    protected function view(): string
    {
        return 'cachet::captcha.hcaptcha';
    }

    protected function responseField(): string
    {
        return 'h-captcha-response';
    }

    protected function verifyUrl(): string
    {
        return 'https://api.hcaptcha.com/siteverify';
    }
}
