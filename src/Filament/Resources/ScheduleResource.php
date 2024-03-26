<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filament\Resources\ScheduleResource\Pages;
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
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->required()
                        ->options(ScheduleStatusEnum::class)
                        ->default(ScheduleStatusEnum::upcoming)
                        ->live(),
                    Forms\Components\MarkdownEditor::make('message')
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->required(),
                    Forms\Components\DateTimePicker::make('completed_at')
                        ->visible(fn (Forms\Get $get): bool => $get('status') === ScheduleStatusEnum::complete),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->visible(fn (Schedule $record): bool => in_array($record->status->value, ScheduleStatusEnum::incomplete()))
                    ->label(__('Complete Maintenance'))
                    ->form([
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->required(),
                    ])
                    ->color('success')
                    ->action(fn (Schedule $record, array $data) => $record->update(['completed_at' => $data['completed_at'], 'status' => ScheduleStatusEnum::complete])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('Schedule');
    }
}
