<?php

namespace Cachet\Filament\Resources\IncidentUpdateResource\Pages;

use Cachet\Filament\Resources\IncidentUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncidentUpdates extends ListRecords
{
    protected static string $resource = IncidentUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
