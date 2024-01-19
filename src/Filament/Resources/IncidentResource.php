<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\IncidentResource\Pages;
use Cachet\Filament\Resources\IncidentResource\RelationManagers\IncidentUpdatesRelationManager;
use Cachet\Models\Incident;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $navigationIcon = 'cachet-incident';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
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
                    Forms\Components\ToggleButtons::make('visible')
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Forms\Components\Toggle::make('stickied')
                        ->required(),
                ]),
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\Select::make('component_id')
                        ->multiple()
                        ->relationship('components', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DateTimePicker::make('occurred_at'),
                    Forms\Components\Select::make('user_id')
                        ->hint(__('The user who reported the incident.'))
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->required()
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('password')
                                ->required()
                                ->password()
                                ->confirmed()
                                ->minLength(8),
                        ]),
                    Forms\Components\Toggle::make('notifications')
                        ->label(__('Send notifications to subscribers.'))
                        ->required(),
                    Forms\Components\TextInput::make('guid')
                        ->required()
                        ->default(fn () => (string) Str::uuid())
                        ->unique(ignoreRecord: true)
                        ->maxLength(36)
                        ->hidden(),
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
                    ->label(__('Record Update'))
                    ->color('info')
                    ->action(function (Incident $record, array $data) {
                        //                        $update = $record->incidentUpdates()->create($data);

                        Notification::make()
                            ->title('Incident Updated')
                            ->body('Incident was updated...')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\MarkdownEditor::make('message')->required(),
                        Forms\Components\ToggleButtons::make('Status')
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
