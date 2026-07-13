<?php

namespace Cachet\Filament\Resources\Incidents\Pages;

use Cachet\Actions\Incident\NotifyIncidentSubscribers;
use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Models\Incident;
use Filament\Resources\Pages\CreateRecord;

class CreateIncident extends CreateRecord
{
    use InteractsWithMeta;

    protected static string $resource = IncidentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractMetaFormData($data);
    }

    protected function afterCreate(): void
    {
        /** @var Incident $incident */
        $incident = $this->record;

        $this->persistMeta();

        app(NotifyIncidentSubscribers::class)->handle($incident);
    }
}
