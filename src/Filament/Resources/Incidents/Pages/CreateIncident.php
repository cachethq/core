<?php

namespace Cachet\Filament\Resources\Incidents\Pages;

use Cachet\Actions\Incident\CreateIncident as CreateIncidentAction;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $requestData = CreateIncidentRequestData::from($data);

        return app(CreateIncidentAction::class)->handle($requestData);
    }
}
