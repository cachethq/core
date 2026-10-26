<?php

namespace Cachet\Settings;

use Cachet\Enums\CaptchaProviderEnum;
use Spatie\LaravelSettings\Settings;

class CaptchaSettings extends Settings
{
    public CaptchaProviderEnum $provider = CaptchaProviderEnum::none;

    public ?string $site_key = null;

    public ?string $secret_key = null;

    public int $altcha_complexity = 50000;

    public static function group(): string
    {
        return 'captcha';
    }

    /**
     * @return list<string>
     */
    public static function encrypted(): array
    {
        return ['secret_key'];
    }
}
