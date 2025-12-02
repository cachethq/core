<?php

namespace Cachet\Filament\Resources\Schedules\RelationManagers;

use Cachet\Enums\ComponentStatusEnum;
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
        return __('Components');
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
            ->modelLabel(__('Component'))
            ->pluralModelLabel(__('Components'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder(__('Select a component')),
                    )
                    ->multiple()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Set a default component_status value (Operational)
                        $data['component_status'] = ComponentStatusEnum::operational->value;

                        return $data;
                    }),
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
