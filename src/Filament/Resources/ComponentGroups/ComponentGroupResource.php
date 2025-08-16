<?php

namespace Cachet\Filament\Resources\ComponentGroups;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\ComponentGroups\Pages\CreateComponentGroup;
use Cachet\Filament\Resources\ComponentGroups\Pages\EditComponentGroup;
use Cachet\Filament\Resources\ComponentGroups\Pages\ListComponentGroups;
use Cachet\Filament\Resources\Components\RelationManagers\ComponentsRelationManager;
use Cachet\Models\ComponentGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComponentGroupResource extends Resource
{
    protected static ?string $model = ComponentGroup::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->schema([
                    TextInput::make('name')
                        ->label(__('cachet::component_group.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),
                    ToggleButtons::make('visible')
                        ->label(__('cachet::component_group.form.visible_label'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required()
                        ->columnSpanFull(),
                    ToggleButtons::make('collapsed')
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
                TextColumn::make('name')
                    ->label(__('cachet::component_group.list.headers.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('visible')
                    ->label(__('cachet::component_group.list.headers.visible'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('collapsed')
                    ->label(__('cachet::component_group.list.headers.collapsed'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('cachet::component_group.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('cachet::component_group.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order')
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
            'index' => ListComponentGroups::route('/'),
            'create' => CreateComponentGroup::route('/create'),
            'edit' => EditComponentGroup::route('/{record}/edit'),
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
