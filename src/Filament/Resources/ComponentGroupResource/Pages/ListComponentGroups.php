<?php

namespace Cachet\Filament\Resources\ComponentGroupResource\Pages;

use Cachet\Filament\Resources\ComponentGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComponentGroups extends ListRecords
{
    protected static string $resource = ComponentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
