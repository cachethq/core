<?php

namespace Cachet\Filament\Resources\IncidentTemplates\Pages;

use Cachet\Filament\Resources\IncidentTemplates\IncidentTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncidentTemplates extends ListRecords
{
    protected static string $resource = IncidentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
