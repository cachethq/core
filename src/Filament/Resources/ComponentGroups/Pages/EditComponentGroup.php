<?php

namespace Cachet\Filament\Resources\ComponentGroups\Pages;

use Cachet\Actions\ComponentGroup\UpdateComponentGroup;
use Cachet\Data\Requests\ComponentGroup\UpdateComponentGroupRequestData;
use Cachet\Filament\Resources\ComponentGroups\ComponentGroupResource;
use Cachet\Models\ComponentGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditComponentGroup extends EditRecord
{
    protected static string $resource = ComponentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var ComponentGroup $record */
        $requestData = UpdateComponentGroupRequestData::from($data);

        app(UpdateComponentGroup::class)->handle($record, $requestData);

        return $record;
    }
}
