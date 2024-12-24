<?php

namespace Cachet\Filament\Resources\IncidentResource\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Components');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name')),
                Forms\Components\ToggleButtons::make('component_status')
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name')),
                Tables\Columns\TextColumn::make('component_status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\ToggleButtons::make('component_status')
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
            ->actions([
                //                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
