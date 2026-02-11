<?php

namespace Cachet\Filament\Resources\Components\Pages;

use Cachet\Filament\Resources\Components\ComponentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComponent extends EditRecord
{
    protected static string $resource = ComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    /**
     * Mutate form data before saving the record.
     * Specifically handles the Uptime Kuma Monitor ID, storing it in the meta field 
    * 

 */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $uptimeKumaMonitorId = $data['uptime_kuma_monitor_id'] ?? null;

        $meta = $data['meta'] ?? $this->record->meta ?? [];

        if (! empty($uptimeKumaMonitorId)) {
            $meta['uptime_kuma_monitor_id'] = (int) $uptimeKumaMonitorId;
        } else {
            unset($meta['uptime_kuma_monitor_id']);
        }

        $data['meta'] = $meta;
        unset($data['uptime_kuma_monitor_id']);

        return $data;
    }
}
