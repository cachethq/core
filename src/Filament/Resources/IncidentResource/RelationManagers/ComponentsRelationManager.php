<?php

namespace Cachet\Filament\Resources\IncidentResource\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\ToggleButtons::make('status')
                    ->inline()
                    ->columnSpanFull()
                    ->options(ComponentStatusEnum::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status')
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
                        Forms\Components\ToggleButtons::make('status')
                            ->inline()
                            ->columnSpanFull()
                            ->options(ComponentStatusEnum::class)
                            ->required(),
                    ])
                    ->model(Component::class),
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
