<?php

namespace Cachet\Filament\Resources\Components\Pages;

use Cachet\Actions\Component\UpdateComponent;
use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Filament\Resources\Components\ComponentResource;
use Cachet\Models\Component;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditComponent extends EditRecord
{
    protected static string $resource = ComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Component $record */
        $requestData = UpdateComponentRequestData::from($data);

        app(UpdateComponent::class)->handle($record, $requestData);

        return $record;
    }
}
