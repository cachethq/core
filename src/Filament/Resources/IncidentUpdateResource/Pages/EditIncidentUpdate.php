<?php

namespace Cachet\Filament\Resources\IncidentUpdateResource\Pages;

use Cachet\Filament\Resources\IncidentUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIncidentUpdate extends EditRecord
{
    protected static string $resource = IncidentUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
