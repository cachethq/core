<?php

namespace Cachet\Filament\Resources\ApiKeyResource\Pages;

use Cachet\Filament\Resources\ApiKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiKeys extends ListRecords
{
    protected static string $view = 'cachet::filament.pages.api-key.index';

    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
