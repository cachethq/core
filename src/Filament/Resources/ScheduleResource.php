<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filament\Resources\ScheduleResource\Pages;
use Cachet\Filament\Resources\UpdateResource\RelationManagers\UpdatesRelationManager;
use Cachet\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                        ->label(__('Name'))
                        ->required(),
                    Forms\Components\MarkdownEditor::make('message')
                        ->label(__('Message'))
                        ->columnSpanFull(),
                ])->columnSpan(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label(__('Scheduled at'))
                        ->required(),
                    Forms\Components\DateTimePicker::make('completed_at')
                        ->label(__('Completed at')),
                ])->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->autocomplete(false),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label(__('Scheduled at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label(__('Completed at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->disabled(fn (Schedule $record): bool => $record->status === ScheduleStatusEnum::complete)
                    ->label(__('Complete Maintenance'))
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
            ->emptyStateHeading(__('Schedules'))
            ->emptyStateDescription(__('Plan and schedule your maintenance.'));
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
        return __('Schedule');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Schedules');
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
