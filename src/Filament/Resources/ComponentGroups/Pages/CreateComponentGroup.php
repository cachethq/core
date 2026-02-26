<?php

namespace Cachet\Filament\Resources\ComponentGroups\Pages;

use Cachet\Actions\ComponentGroup\CreateComponentGroup as CreateComponentGroupAction;
use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Filament\Resources\ComponentGroups\ComponentGroupResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateComponentGroup extends CreateRecord
{
    protected static string $resource = ComponentGroupResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $requestData = CreateComponentGroupRequestData::from($data);

        return app(CreateComponentGroupAction::class)->handle($requestData);
    }
}
