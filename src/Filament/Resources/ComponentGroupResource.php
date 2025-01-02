<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\ComponentGroupResource\Pages;
use Cachet\Filament\Resources\ComponentResource\RelationManagers\ComponentsRelationManager;
use Cachet\Models\ComponentGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComponentGroupResource extends Resource
{
    protected static ?string $model = ComponentGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('cachet::component_group.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),
                    Forms\Components\ToggleButtons::make('visible')
                        ->label(__('cachet::component_group.form.visible_label'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\ToggleButtons::make('collapsed')
                        ->label(__('cachet::component_group.form.collapsed_label'))
                        ->required()
                        ->inline()
                        ->options(ComponentGroupVisibilityEnum::class)
                        ->default(ComponentGroupVisibilityEnum::expanded)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('cachet::component_group.list.headers.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('visible')
                    ->label(__('cachet::component_group.list.headers.visible'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('collapsed')
                    ->label(__('cachet::component_group.list.headers.collapsed'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cachet::component_group.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('cachet::component_group.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('cachet::component_group.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::component_group.list.empty_state.description'));
    }

    public static function getRelations(): array
    {
        return [
            ComponentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComponentGroups::route('/'),
            'create' => Pages\CreateComponentGroup::route('/create'),
            'edit' => Pages\EditComponentGroup::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::component_group.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::component_group.resource_label', 2);
    }
}
