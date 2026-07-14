<?php

namespace Cachet\Filament\Resources\Components\Pages;

use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\Components\ComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComponent extends CreateRecord
{
    use InteractsWithMeta;

    protected static string $resource = ComponentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractMetaFormData($data);
    }

    protected function afterCreate(): void
    {
        $this->persistMeta();
    }
}
