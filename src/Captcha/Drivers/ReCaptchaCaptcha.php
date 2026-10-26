<?php

namespace Cachet\Captcha\Drivers;

/**
 * Google reCAPTCHA v2 (checkbox).
 */
class ReCaptchaCaptcha extends SiteVerifyCaptcha
{
    protected function view(): string
    {
        return 'cachet::captcha.recaptcha';
    }

    protected function responseField(): string
    {
        return 'g-recaptcha-response';
    }

    protected function verifyUrl(): string
    {
        return 'https://www.google.com/recaptcha/api/siteverify';
    }
}
