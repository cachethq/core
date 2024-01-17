<?php

namespace Cachet\Filament\Resources\SubscriberResource\Pages;

use Cachet\Filament\Resources\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscriber extends EditRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
