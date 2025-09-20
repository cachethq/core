<?php

namespace Cachet\Filament\Resources\Incidents\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Components');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name')),
                ToggleButtons::make('component_status')
                    ->label(__('Status'))
                    ->inline()
                    ->options(ComponentStatusEnum::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modelLabel(__('Component'))
            ->pluralModelLabel(__('Components'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name')),
                TextColumn::make('component_status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        ToggleButtons::make('component_status')
                            ->label(__('Status'))
                            ->inline()
                            ->columnSpanFull()
                            ->options(ComponentStatusEnum::class)
                            ->required(),
                    ])
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder(__('Select a component')),
                    )
                    ->multiple(),
            ])
            ->recordActions([
                //                Tables\Actions\EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
