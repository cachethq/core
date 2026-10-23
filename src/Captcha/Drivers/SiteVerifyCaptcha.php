<?php

namespace Cachet\Captcha\Drivers;

use Cachet\Cachet;
use Cachet\Captcha\Captcha;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * A hosted captcha whose responses are checked against the provider's "siteverify" endpoint.
 */
abstract class SiteVerifyCaptcha implements Captcha
{
    public function __construct(
        protected readonly ?string $siteKey,
        protected readonly ?string $secretKey,
    ) {}

    /**
     * The view that renders the provider's widget.
     */
    abstract protected function view(): string;

    /**
     * The name of the form field the widget fills with its response token.
     */
    abstract protected function responseField(): string;

    /**
     * The provider's verification endpoint.
     */
    abstract protected function verifyUrl(): string;

    public function enabled(): bool
    {
        return filled($this->siteKey) && filled($this->secretKey);
    }

    public function render(): Htmlable
    {
        return view($this->view(), [
            'siteKey' => $this->siteKey,
            'locale' => str_replace('_', '-', app()->getLocale()),
        ]);
    }

    public function verify(Request $request): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        $token = $request->input($this->responseField());

        if (! is_string($token) || $token === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->withUserAgent(Cachet::USER_AGENT)
                ->timeout(5)
                ->post($this->verifyUrl(), [
                    'secret' => $this->secretKey,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (Throwable $exception) {
            Log::warning('Captcha verification request failed.', [
                'provider' => static::class,
                'exception' => $exception->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Captcha verification request failed.', [
                'provider' => static::class,
                'status' => $response->status(),
            ]);

            return false;
        }

        return $response->json('success') === true;
    }
}
