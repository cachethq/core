<?php

namespace Cachet\Filament\Resources\Incidents\Pages;

use Cachet\Filament\Resources\Incidents\IncidentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIncident extends EditRecord
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            IncidentResource::recordUpdateAction()
                ->label(__('cachet::incident.record_update.new_update_label'))
                ->icon('heroicon-o-plus'),
            DeleteAction::make(),
        ];
    }
}
