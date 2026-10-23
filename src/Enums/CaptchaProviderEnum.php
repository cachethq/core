<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasLabel;

enum CaptchaProviderEnum: string implements HasLabel
{
    case none = 'none';
    case altcha = 'altcha';
    case turnstile = 'turnstile';
    case hcaptcha = 'hcaptcha';
    case recaptcha = 'recaptcha';

    public function getLabel(): string
    {
        return __('cachet::settings.manage_captcha.providers.'.$this->value);
    }

    /**
     * Determine whether the provider needs a site key and a secret key.
     */
    public function requiresKeys(): bool
    {
        return in_array($this, [self::turnstile, self::hcaptcha, self::recaptcha], true);
    }

    /**
     * The URL where the provider's site key and secret key are created.
     */
    public function keysUrl(): ?string
    {
        return match ($this) {
            self::turnstile => 'https://dash.cloudflare.com/?to=/:account/turnstile',
            self::hcaptcha => 'https://dashboard.hcaptcha.com/sites',
            self::recaptcha => 'https://www.google.com/recaptcha/admin',
            default => null,
        };
    }
}
