<?php

namespace Cachet\Filament\Resources\Components\Pages;

use Cachet\Actions\Component\CreateComponent as CreateComponentAction;
use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Filament\Resources\Components\ComponentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateComponent extends CreateRecord
{
    protected static string $resource = ComponentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $requestData = CreateComponentRequestData::from($data);

        return app(CreateComponentAction::class)->handle($requestData);
    }
}
