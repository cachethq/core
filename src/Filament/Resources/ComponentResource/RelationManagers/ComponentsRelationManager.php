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
                    ->label(__('cachet::component.form.name_label'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('cachet::component.form.status_label'))
                    ->inline()
                    ->columnSpanFull()
                    ->options(ComponentStatusEnum::class)
                    ->required(),
                Forms\Components\MarkdownEditor::make('description')
                    ->label(__('cachet::component.form.description_label'))
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('link')
                    ->label(__('cachet::component.form.link_label'))
                    ->url()
                    ->helperText(__('cachet::component.form.link_helper')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modelLabel(trans_choice('cachet::component.resource_label', 1))
            ->pluralModelLabel(trans_choice('cachet::component.resource_label', 2))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('cachet::component.list.headers.name')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('cachet::component.list.headers.status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->label(__('cachet::component.list.headers.enabled'))
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
