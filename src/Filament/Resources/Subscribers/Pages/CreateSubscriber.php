<?php

namespace Cachet\Filament\Resources\Subscribers\Pages;

use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\Subscribers\SubscriberResource;
use Cachet\Models\Subscriber;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriber extends CreateRecord
{
    use InteractsWithMeta;

    protected static string $resource = SubscriberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractMetaFormData($data);
    }

    protected function afterCreate(): void
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->record;

        $this->persistMeta();

        if (! $subscriber->hasVerifiedEmail()) {
            $subscriber->sendEmailVerificationNotification();
        }
    }
}
