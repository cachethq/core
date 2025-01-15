<?php

namespace Cachet\Filament\Resources\ApiKeyResource\Pages;

use Cachet\Filament\Resources\ApiKeyResource;
use Cachet\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Laravel\Sanctum\NewAccessToken;

class ListApiKeys extends ListRecords
{
    protected static string $view = 'cachet::filament.pages.api-key.index';

    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
