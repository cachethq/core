<?php

namespace Cachet\Filament\Resources\ComponentResource\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
use Filament\Forms;
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
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('Status'))
                    ->inline()
                    ->columnSpanFull()
                    ->options(ComponentStatusEnum::class)
                    ->required(),
                Forms\Components\MarkdownEditor::make('description')
                    ->label(__('Description'))
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('link')
                    ->label(__('Link'))
                    ->url()
                    ->hint(__('An optional link to the component.')),
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
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->label(__('Enabled'))
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
