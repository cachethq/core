<?php

namespace Cachet\Filament\Resources\Components\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecksRelationManager extends RelationManager
{
    protected static string $relationship = 'checks';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('cachet::component.checks.title'))
            ->modelLabel(__('cachet::component.checks.title'))
            ->columns([
                TextColumn::make('status')
                    ->label(__('cachet::component.checks.headers.status'))
                    ->badge()
                    ->sortable(),
                IconColumn::make('successful')
                    ->label(__('cachet::component.checks.headers.successful'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('response_code')
                    ->label(__('cachet::component.checks.headers.response_code'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('response_time')
                    ->label(__('cachet::component.checks.headers.response_time'))
                    ->placeholder('—')
                    ->suffix(' ms')
                    ->sortable(),
                TextColumn::make('checked_at')
                    ->label(__('cachet::component.checks.headers.checked_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('checked_at', 'desc')
            ->emptyStateHeading(__('cachet::component.checks.empty_state.heading'))
            ->emptyStateDescription(__('cachet::component.checks.empty_state.description'));
    }
}
