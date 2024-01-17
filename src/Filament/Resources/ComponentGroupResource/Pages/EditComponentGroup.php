<?php

namespace Cachet\Filament\Resources\ComponentGroupResource\Pages;

use Cachet\Filament\Resources\ComponentGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComponentGroup extends EditRecord
{
    protected static string $resource = ComponentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
