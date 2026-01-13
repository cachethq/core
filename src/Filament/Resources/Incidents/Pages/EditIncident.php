<?php

namespace Cachet\Filament\Resources\Incidents\Pages;

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Models\Incident;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Incident $record */
        $requestData = UpdateIncidentRequestData::from($data);

        app(UpdateIncident::class)->handle($record, $requestData);

        return $record;
    }
}
