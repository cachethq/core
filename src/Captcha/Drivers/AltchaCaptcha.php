<?php

namespace Cachet\Captcha\Drivers;

use Cachet\Captcha\Captcha;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Self-hosted proof-of-work captcha compatible with the ALTCHA protocol (https://altcha.org).
 *
 * The browser has to find the number that, appended to the salt, hashes to the challenge.
 * Challenges are signed with an HMAC, expire and can only be used once, so no state is kept
 * until a solution is submitted.
 */
class AltchaCaptcha implements Captcha
{
    /**
     * How long a challenge can be solved and submitted, in seconds.
     */
    public const int TTL = 300;

    public function __construct(
        private readonly string $key,
        private readonly int $complexity,
    ) {}

    public function enabled(): bool
    {
        return true;
    }

    public function render(): Htmlable
    {
        return view('cachet::captcha.altcha', [
            'challengeUrl' => route('cachet.subscribers.captcha'),
        ]);
    }

    /**
     * Create a new signed challenge.
     *
     * @return array{algorithm: string, challenge: string, maxnumber: int, salt: string, signature: string}
     */
    public function challenge(): array
    {
        $salt = bin2hex(random_bytes(12)).'?expires='.(now()->getTimestamp() + self::TTL);
        $challenge = hash('sha256', $salt.random_int(0, $this->maxNumber()));

        return [
            'algorithm' => 'SHA-256',
            'challenge' => $challenge,
            'maxnumber' => $this->maxNumber(),
            'salt' => $salt,
            'signature' => $this->sign($challenge),
        ];
    }

    public function verify(Request $request): bool
    {
        $payload = json_decode(base64_decode((string) $request->input('altcha'), true) ?: '', true);

        if (! is_array($payload)) {
            return false;
        }

        ['algorithm' => $algorithm, 'challenge' => $challenge, 'number' => $number, 'salt' => $salt, 'signature' => $signature] = $payload + array_fill_keys(['algorithm', 'challenge', 'number', 'salt', 'signature'], null);

        if ($algorithm !== 'SHA-256' || ! is_string($challenge) || ! is_string($salt) || ! is_string($signature) || ! is_int($number)) {
            return false;
        }

        parse_str((string) parse_url($salt, PHP_URL_QUERY), $params);
        $expires = (int) ($params['expires'] ?? 0);

        if ($expires < now()->getTimestamp()) {
            return false;
        }

        if (! hash_equals($this->sign($challenge), $signature) || ! hash_equals($challenge, hash('sha256', $salt.$number))) {
            return false;
        }

        // Each solved challenge can only be submitted once.
        return Cache::add('cachet:captcha:altcha:'.$signature, true, now()->setTimestamp($expires));
    }

    private function sign(string $challenge): string
    {
        return hash_hmac('sha256', $challenge, $this->key);
    }

    private function maxNumber(): int
    {
        return max(1, $this->complexity);
    }
}
