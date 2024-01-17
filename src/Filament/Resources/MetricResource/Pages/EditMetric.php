<?php

namespace Cachet\Filament\Resources\MetricResource\Pages;

use Cachet\Filament\Resources\MetricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMetric extends EditRecord
{
    protected static string $resource = MetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
