<?php

namespace Cachet\Filament\Resources\WebhookSubscriptionResource\Pages;

use Cachet\Filament\Resources\WebhookSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebhookSubscription extends EditRecord
{
    protected static string $resource = WebhookSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
