<?php

namespace Cachet\Filament\Resources\WebhookSubscriptions\Pages;

use Cachet\Filament\Resources\WebhookSubscriptions\WebhookSubscriptionResource;
use Cachet\Models\WebhookSubscription;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditWebhookSubscription extends EditRecord
{
    protected static string $resource = WebhookSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function content(Schema $schema): Schema
    {
        /** @var WebhookSubscription $webhookSubscription */
        $webhookSubscription = $this->getRecord();

        return $schema
            ->components([
                $this->getFormContentComponent(),
                $this->getRelationManagersContentComponent(),
                view('cachet::filament.widgets.webhook-attempts', [
                    'attempts' => $webhookSubscription->attempts,
                ]),
            ]);
    }
}
