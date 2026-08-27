<?php

namespace Cachet\Filament\Resources\Schedules\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Components\ComponentOptions;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
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
                // No form editing needed - components are only attached/detached
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->modalHeading(__('cachet::component.attach.heading'))
                    ->multiple()
                    ->form(fn (): array => [
                        Select::make('recordId')
                            ->label(trans_choice('cachet::component.resource_label', 2))
                            ->placeholder(__('cachet::component.attach.placeholder'))
                            ->options(fn (): array => ComponentOptions::forSelect($this->getOwnerRecord()))
                            ->searchable()
                            ->multiple()
                            ->required(),
                        Select::make('component_status')
                            ->options(ComponentStatusEnum::class)
                            ->default(ComponentStatusEnum::under_maintenance->value)
                            ->required()
                            ->label(__('cachet::schedule.form.add_component.status_label')),
                    ]),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
