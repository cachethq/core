<?php

namespace Cachet\Filament\Resources;

use Cachet\Actions\Update\CreateUpdate as CreateIncidentUpdateAction;
use Cachet\Data\IncidentUpdate\CreateIncidentUpdateData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\IncidentResource\Pages;
use Cachet\Filament\Resources\IncidentResource\RelationManagers\ComponentsRelationManager;
use Cachet\Filament\Resources\UpdateResource\RelationManagers\UpdatesRelationManager;
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
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),
                    Forms\Components\ToggleButtons::make('status')
                        ->label(__('Status'))
                        ->inline()
                        ->columnSpanFull()
                        ->options(IncidentStatusEnum::class)
                        ->required(),
                    Forms\Components\MarkdownEditor::make('message')
                        ->label(__('Message'))
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('occurred_at')
                        ->label(__('Occurred at'))
                        ->helperText(__('The incident\'s created timestamp will be used if left empty.')),
                    Forms\Components\ToggleButtons::make('visible')
                        ->label(__('Visible'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Forms\Components\Repeater::make('incidentComponents')
                        ->relationship()
                        ->defaultItems(0)
                        ->addActionLabel('Add component')
                        ->schema([
                            Forms\Components\Select::make('component_id')
                                ->preload()
                                ->required()
                                ->relationship('component', 'name')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->label(__('Component')),
                            Forms\Components\ToggleButtons::make('component_status')
                                ->label(__('Status'))
                                ->inline()
                                ->options(ComponentStatusEnum::class)
                                ->required(),
                        ])
                        ->label('Components')
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
                    Forms\Components\TextInput::make('guid')
                        ->label('Incident UUID')
                        ->visibleOn(['edit'])
                        ->disabled()
                        ->readonly()
                        ->columnSpanFull(),
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
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('visible')
                    ->label(__('Visible'))
                    ->sortable()
                    ->badge(),
                Tables\Columns\IconColumn::make('stickied')
                    ->label(__('Stickied'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label(__('Occurred at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('notifications')
                    ->label(__('Notified Subscribers'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(IncidentStatusEnum::class),
            ])
            ->actions([
                Action::make('add-update')
                    ->disabled(fn (Incident $record) => $record->status === IncidentStatusEnum::fixed)
                    ->label(__('Record Update'))
                    ->color('info')
                    ->action(function (CreateIncidentUpdateAction $createIncidentUpdate, Incident $record, array $data) {
                        $createIncidentUpdate->handle($record, CreateIncidentUpdateData::from($data));

                        Notification::make()
                            ->title(__('Incident :name Updated', ['name' => $record->name]))
                            ->body(__('A new incident update has been recorded.'))
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\MarkdownEditor::make('message')
                            ->label(__('Message'))
                            ->required(),
                        Forms\Components\ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options(IncidentStatusEnum::class)
                            ->inline()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label(__('User'))
                            ->hint(__('Who reported this incident.'))
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->searchable()
                            ->preload(),
                    ]),
                Action::make('view-incident')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Incident $record): string => route('cachet.status-page.incident', $record))
                    ->label(__('View Incident')),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('Incidents'))
            ->emptyStateDescription(__('Incidents are used to communicate and track the status of your services.'));
    }

    public static function getRelations(): array
    {
        return [
            ComponentsRelationManager::class,
            UpdatesRelationManager::class,
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

    public static function getPluralLabel(): ?string
    {
        return __('Incidents');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::unresolved()->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        if ((int) static::getNavigationBadge() > 0) {
            return 'danger';
        }

        return 'success';
    }
}
