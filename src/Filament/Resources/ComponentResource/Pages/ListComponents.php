<?php

namespace Cachet\Filament\Resources\ComponentResource\Pages;

use Cachet\Filament\Resources\ComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComponents extends ListRecords
{
    protected static string $resource = ComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
