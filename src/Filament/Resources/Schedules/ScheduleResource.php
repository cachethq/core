<?php

namespace Cachet\Filament\Resources\Schedules;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filament\Resources\Schedules\Pages\CreateSchedule;
use Cachet\Filament\Resources\Schedules\Pages\EditSchedule;
use Cachet\Filament\Resources\Schedules\Pages\ListSchedules;
use Cachet\Filament\Resources\Schedules\RelationManagers\ComponentsRelationManager;
use Cachet\Filament\Resources\Updates\RelationManagers\UpdatesRelationManager;
use Cachet\Models\Schedule;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'cachet-maintenance';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('cachet::schedule.form.name_label'))
                        ->required(),
                    MarkdownEditor::make('message')
                        ->label(__('cachet::schedule.form.message_label'))
                        ->columnSpanFull(),
                    Repeater::make('scheduleComponents')
                        ->visibleOn('create')
                        ->relationship()
                        ->defaultItems(0)
                        ->addActionLabel(__('Add Component'))
                        ->schema([
                            Select::make('component_id')
                                ->preload()
                                ->required()
                                ->relationship('component', 'name')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->label(__('Component')),
                            Hidden::make('component_status')
                                ->default(ComponentStatusEnum::operational->value),
                        ])
                        ->label(__('Affected Components'))
                        ->columnSpanFull(),
                ])->columnSpan(3),
                Section::make()->schema([
                    DateTimePicker::make('scheduled_at')
                        ->label(__('cachet::schedule.form.scheduled_at_label'))
                        ->native(false) // Fixes #288 (Filament DateTimePicker does not display time selection on Firefox)
                        ->required(),
                    DateTimePicker::make('completed_at')
                        ->label(__('cachet::schedule.form.completed_at_label'))
                        ->native(false), // Fixes #288 (Filament DateTimePicker does not display time selection on Firefox)
                ])->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::schedule.list.headers.name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('cachet::schedule.list.headers.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->label(__('cachet::schedule.list.headers.scheduled_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label(__('cachet::schedule.list.headers.completed_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('cachet::schedule.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('cachet::schedule.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('cachet::schedule.list.headers.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('add-update')
                    ->disabled(fn (Schedule $record) => $record->status === ScheduleStatusEnum::complete)
                    ->label(__('cachet::schedule.list.actions.record_update'))
                    ->color('info')
                    ->action(function (CreateUpdate $createUpdate, Schedule $record, array $data) {
                        $createUpdate->handle($record, CreateScheduleUpdateRequestData::from($data));

                        Notification::make()
                            ->title(__('cachet::schedule.add_update.success_title', ['name' => $record->name]))
                            ->body(__('cachet::schedule.add_update.success_body'))
                            ->success()
                            ->send();
                    })
                    ->schema([
                        MarkdownEditor::make('message')
                            ->label(__('cachet::schedule.add_update.form.message_label'))
                            ->required(),

                        DateTimePicker::make('completed_at')
                            ->label(__('cachet::schedule.add_update.form.completed_at_label')),
                    ]),
                Action::make('complete')
                    ->disabled(fn (Schedule $record): bool => $record->status === ScheduleStatusEnum::complete)
                    ->label(__('cachet::schedule.list.actions.complete'))
                    ->schema([
                        DateTimePicker::make('completed_at')
                            ->required(),
                    ])
                    ->color('success')
                    ->action(fn (Schedule $record, array $data) => $record->update(['completed_at' => $data['completed_at']])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('cachet::schedule.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::schedule.list.empty_state.description'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ComponentsRelationManager::class,
            UpdatesRelationManager::class,
        ];
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::schedule.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::schedule.resource_label', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Schedule::inTheFuture()->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        if ((int) static::getNavigationBadge() > 0) {
            return 'warning';
        }

        return 'success';
    }
}
