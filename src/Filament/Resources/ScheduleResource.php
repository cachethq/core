<?php

namespace Cachet\Filament\Resources;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\ScheduleUpdate\CreateScheduleUpdateData;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filament\Resources\ScheduleResource\Pages;
use Cachet\Filament\Resources\UpdateResource\RelationManagers\UpdatesRelationManager;
use Cachet\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'cachet-maintenance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('cachet::schedule.form.name_label'))
                        ->required(),
                    Forms\Components\MarkdownEditor::make('message')
                        ->label(__('cachet::schedule.form.message_label'))
                        ->columnSpanFull(),
                ])->columnSpan(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label(__('cachet::schedule.form.scheduled_at_label'))
                        ->required(),
                    Forms\Components\DateTimePicker::make('completed_at')
                        ->label(__('cachet::schedule.form.completed_at_label')),
                ])->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('cachet::schedule.list.headers.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('cachet::schedule.list.headers.status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label(__('cachet::schedule.list.headers.scheduled_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label(__('cachet::schedule.list.headers.completed_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cachet::schedule.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('cachet::schedule.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('cachet::schedule.list.headers.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('add-update')
                    ->disabled(fn (Schedule $record) => $record->status === ScheduleStatusEnum::complete)
                    ->label(__('cachet::schedule.list.actions.record_update'))
                    ->color('info')
                    ->action(function (CreateUpdate $createUpdate, Schedule $record, array $data) {
                        $createUpdate->handle($record, CreateScheduleUpdateData::from($data));

                        Notification::make()
                            ->title(__('cachet::schedule.add_update.success_title', ['name' => $record->name]))
                            ->body(__('cachet::schedule.add_update.success_body'))
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\MarkdownEditor::make('message')
                            ->label(__('cachet::schedule.add_update.form.message_label'))
                            ->required(),

                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label(__('cachet::schedule.add_update.form.completed_at_label')),
                    ]),
                Tables\Actions\Action::make('complete')
                    ->disabled(fn (Schedule $record): bool => $record->status === ScheduleStatusEnum::complete)
                    ->label(__('cachet::schedule.list.actions.complete'))
                    ->form([
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->required(),
                    ])
                    ->color('success')
                    ->action(fn (Schedule $record, array $data) => $record->update(['completed_at' => $data['completed_at']])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('cachet::schedule.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::schedule.list.empty_state.description'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
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
        return static::getModel()::incomplete()->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        if ((int) static::getNavigationBadge() > 0) {
            return 'warning';
        }

        return 'success';
    }
}
