<?php

namespace Cachet\Filament\Resources\Incidents\Pages;

use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIncident extends EditRecord
{
    use InteractsWithMeta;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillMetaFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractMetaFormData($data);
    }

    protected function afterSave(): void
    {
        $this->persistMeta();
    }
}
