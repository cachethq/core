<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\IncidentResource\Pages;
use Cachet\Filament\Resources\IncidentResource\RelationManagers\ComponentsRelationManager;
use Cachet\Filament\Resources\IncidentResource\RelationManagers\IncidentUpdatesRelationManager;
use Cachet\Models\Incident;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $navigationIcon = 'cachet-incident';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\ToggleButtons::make('status')
                        ->inline()
                        ->columnSpanFull()
                        ->options(IncidentStatusEnum::class)
                        ->required(),
                    Forms\Components\MarkdownEditor::make('message')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('occurred_at')
                        ->helperText(__('The incident\'s created timestamp will be used if left empty.')),
                    Forms\Components\ToggleButtons::make('visible')
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    //                    Forms\Components\Select::make('component')
                    //                        ->multiple()
                    //                        ->relationship('components', 'name')
                    //                        ->searchable()
                    //                        ->preload(),
                ])
                    ->columnSpan(3),
                Section::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->label(__('User'))
                        ->hint(__('Who reported this incident.'))
                        ->relationship('user', 'name')
                        ->default(auth()->id())
                        ->searchable()
                        ->preload(),
                    Forms\Components\Toggle::make('notifications')
                        ->label(__('Notify Subscribers?'))
                        ->required(),
                    Forms\Components\Toggle::make('stickied')
                        ->label(__('Sticky Incident?'))
                        ->required(),
                ])
                    ->columnSpan(1),
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('visible')
                    ->sortable()
                    ->badge(),
                Tables\Columns\IconColumn::make('stickied')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\TextColumn::make('occurred_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('notifications')
                    ->label(__('Notified Subscribers'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(IncidentStatusEnum::class),
            ])
            ->actions([
                Action::make('add-update')
                    ->disabled(fn (Incident $record) => $record->status === IncidentStatusEnum::fixed)
                    ->label(__('Record Update'))
                    ->color('info')
                    ->action(function (CreateIncidentUpdateAction $createIncidentUpdate, Incident $record, array $data) {
                        $createIncidentUpdate->handle($record, $data);

                        Notification::make()
                            ->title(__('Incident :name Updated', ['name' => $record->name]))
                            ->body(__('A new incident update has been recorded.'))
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\MarkdownEditor::make('message')->required(),
                        Forms\Components\ToggleButtons::make('status')
                            ->options(IncidentStatusEnum::class)
                            ->inline()
                            ->required(),
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            ComponentsRelationManager::class,
            IncidentUpdatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('Incident');
    }
}
