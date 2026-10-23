<?php

namespace Cachet\Captcha;

use Cachet\Captcha\Drivers\AltchaCaptcha;
use Cachet\Captcha\Drivers\HCaptchaCaptcha;
use Cachet\Captcha\Drivers\NullCaptcha;
use Cachet\Captcha\Drivers\ReCaptchaCaptcha;
use Cachet\Captcha\Drivers\TurnstileCaptcha;
use Cachet\Settings\CaptchaSettings;
use Illuminate\Support\Manager;

/**
 * @mixin Captcha
 */
class CaptchaManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->settings()->provider->value;
    }

    protected function createNoneDriver(): Captcha
    {
        return new NullCaptcha;
    }

    protected function createAltchaDriver(): Captcha
    {
        return new AltchaCaptcha(
            key: hash_hmac('sha256', 'cachet-captcha-altcha', (string) $this->config->get('app.key')),
            complexity: $this->settings()->altcha_complexity,
        );
    }

    protected function createTurnstileDriver(): Captcha
    {
        return new TurnstileCaptcha($this->settings()->site_key, $this->settings()->secret_key);
    }

    protected function createHcaptchaDriver(): Captcha
    {
        return new HCaptchaCaptcha($this->settings()->site_key, $this->settings()->secret_key);
    }

    protected function createRecaptchaDriver(): Captcha
    {
        return new ReCaptchaCaptcha($this->settings()->site_key, $this->settings()->secret_key);
    }

    private function settings(): CaptchaSettings
    {
        return $this->container->make(CaptchaSettings::class);
    }
}
