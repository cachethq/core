<?php

namespace Cachet\Http\Controllers\Subscribers;

use Cachet\Captcha\CaptchaManager;
use Cachet\Captcha\Drivers\AltchaCaptcha;
use Cachet\Settings\MailSettings;
use Illuminate\Http\JsonResponse;

class SubscriberCaptchaController
{
    /**
     * Issue a proof-of-work challenge for the subscribe form.
     */
    public function __invoke(MailSettings $mailSettings, CaptchaManager $captcha): JsonResponse
    {
        $driver = $captcha->driver();

        abort_unless($mailSettings->allow_subscribers && $driver instanceof AltchaCaptcha, 404);

        return response()
            ->json($driver->challenge())
            ->header('Cache-Control', 'no-store');
    }
}
