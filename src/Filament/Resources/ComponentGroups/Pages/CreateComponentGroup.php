<?php

namespace Cachet\Filament\Resources\ComponentGroups\Pages;

use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\ComponentGroups\ComponentGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComponentGroup extends CreateRecord
{
    use InteractsWithMeta;

    protected static string $resource = ComponentGroupResource::class;

    protected function afterCreate(): void
    {
        $this->persistMeta();
    }
}
