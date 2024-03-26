<?php

namespace Cachet\Filament\Resources\IncidentResource\Pages;

use Cachet\Filament\Resources\IncidentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
