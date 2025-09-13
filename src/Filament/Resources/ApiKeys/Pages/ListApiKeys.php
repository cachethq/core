<?php

namespace Cachet\Filament\Resources\ApiKeys\Pages;

use Cachet\Filament\Resources\ApiKeys\ApiKeyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiKeys extends ListRecords
{
    protected string $view = 'cachet::filament.pages.api-key.index';

    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
