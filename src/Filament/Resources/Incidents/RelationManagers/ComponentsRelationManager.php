<?php

namespace Cachet\Filament\Resources\Incidents\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Models\Incident;
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
        return trans_choice('cachet::component.resource_label', 2);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('cachet::component.form.name_label')),
                ToggleButtons::make('component_status')
                    ->label(__('cachet::component.form.status_label'))
                    ->inline()
                    ->options(ComponentStatusEnum::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modelLabel(trans_choice('cachet::component.resource_label', 1))
            ->pluralModelLabel(trans_choice('cachet::component.resource_label', 2))
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::component.list.headers.name')),
                TextColumn::make('component_status')
                    ->label(__('cachet::component.list.headers.status'))
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->modalHeading(__('cachet::component.attach.heading'))
                    ->form(fn (): array => [
                        Select::make('recordId')
                            ->label(trans_choice('cachet::component.resource_label', 2))
                            ->placeholder(__('cachet::component.attach.placeholder'))
                            ->options(function (): array {
                                $owner = $this->getOwnerRecord();
                                return IncidentResource::getComponentOptions($owner instanceof Incident ? $owner : null);
                            })
                            ->searchable()
                            ->multiple()
                            ->required(),
                        ToggleButtons::make('component_status')
                            ->label(__('cachet::component.form.status_label'))
                            ->inline()
                            ->columnSpanFull()
                            ->options(ComponentStatusEnum::class)
                            ->required(),
                    ])
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
