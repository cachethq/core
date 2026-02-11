<?php

namespace Cachet\Filament\Resources\Components\Pages;

use Cachet\Filament\Resources\Components\ComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComponent extends CreateRecord
{
    protected static string $resource = ComponentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $uptimeKumaMonitorId = $data['uptime_kuma_monitor_id'] ?? null;

        $meta = $data['meta'] ?? [];

        if (! empty($uptimeKumaMonitorId)) {
            $meta['uptime_kuma_monitor_id'] = (int) $uptimeKumaMonitorId;
        }

        $data['meta'] = $meta;
        unset($data['uptime_kuma_monitor_id']);

        return $data;
    }
}
