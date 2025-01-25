<?php

declare(strict_types=1);

namespace Cachet\Data\Requests\Subscriber;

use Cachet\Data\BaseData;
use Cachet\Models\Subscriber;
use Illuminate\Auth\Events\Verified;
use Spatie\LaravelData\Attributes\FromRouteParameter;

final class VerifySubscriberEmailRequestData extends BaseData
{
    public function __construct(
        #[FromRouteParameter('subscriber')]
        public readonly int $subscriber,
        #[FromRouteParameter('hash')]
        public readonly string $hash,
    ) {}

    public static function authorize(): bool
    {
        $subscriber = Subscriber::query()->find(request()->route('subscriber'));

        return hash_equals(sha1($subscriber->verify_code), (string) request()->route('hash'));
    }

    public function fulfil(): void
    {
        $subscriber = Subscriber::query()->find($this->subscriber);

        if (! $subscriber->hasVerifiedEmail()) {
            $subscriber->verify();

            event(new Verified($subscriber));
        }
    }
}
