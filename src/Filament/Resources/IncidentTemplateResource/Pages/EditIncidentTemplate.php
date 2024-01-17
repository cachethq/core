<?php

namespace Cachet\Filament\Resources\IncidentTemplateResource\Pages;

use Cachet\Filament\Resources\IncidentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIncidentTemplate extends EditRecord
{
    protected static string $resource = IncidentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
