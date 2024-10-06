<?php

namespace Cachet\Filament\Resources\ComponentResource\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
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
                Forms\Components\MarkdownEditor::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('link')
                    ->url()
                    ->hint(__('An optional link to the component.')),
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
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order');
    }
}
