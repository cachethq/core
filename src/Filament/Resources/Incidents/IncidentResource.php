<?php

namespace Cachet\Filament\Resources\Incidents;

use Cachet\Actions\Update\CreateUpdate as CreateIncidentUpdateAction;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\Incidents\Pages\CreateIncident;
use Cachet\Filament\Resources\Incidents\Pages\EditIncident;
use Cachet\Filament\Resources\Incidents\Pages\ListIncidents;
use Cachet\Filament\Resources\Incidents\RelationManagers\ComponentsRelationManager;
use Cachet\Filament\Resources\Updates\RelationManagers\UpdatesRelationManager;
use Cachet\Models\Incident;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static string|\BackedEnum|null $navigationIcon = 'cachet-incident';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('cachet::incident.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),
                    ToggleButtons::make('status')
                        ->label(__('cachet::incident.form.name_label'))
                        ->inline()
                        ->columnSpanFull()
                        ->options(IncidentStatusEnum::class)
                        ->required(),
                    MarkdownEditor::make('message')
                        ->label(__('cachet::incident.form.message_label'))
                        ->required()
                        ->columnSpanFull(),
                    DateTimePicker::make('occurred_at')
                        ->label(__('cachet::incident.form.occurred_at_label'))
                        ->helperText(__('cachet::incident.form.occurred_at_helper')),
                    ToggleButtons::make('visible')
                        ->label(__('cachet::incident.form.visible_label'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Repeater::make('incidentComponents')
                        ->visibleOn('create')
                        ->relationship()
                        ->defaultItems(0)
                        ->addActionLabel(__('cachet::incident.form.add_component.action_label'))
                        ->schema([
                            Select::make('component_id')
                                ->preload()
                                ->required()
                                ->relationship('component', 'name')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->label(__('cachet::incident.form.add_component.component_label')),
                            ToggleButtons::make('component_status')
                                ->label(__('cachet::incident.form.add_component.status_label'))
                                ->inline()
                                ->options(ComponentStatusEnum::class)
                                ->required(),
                        ])
                        ->label(__('cachet::incident.form.add_component.header')),
                ])
                    ->columnSpan(3),
                Section::make()->schema([
                    Select::make('user_id')
                        ->label(__('User'))
                        ->helperText(__('cachet::incident.form.user_helper'))
                        ->relationship('user', 'name')
                        ->default(auth()->id())
                        ->searchable()
                        ->preload(),
                    Toggle::make('notifications')
                        ->label(__('cachet::incident.form.notifications_label'))
                        ->required(),
                    Toggle::make('stickied')
                        ->label(__('cachet::incident.form.stickied_label'))
                        ->required(),
                    TextInput::make('guid')
                        ->label(__('cachet::incident.form.guid_label'))
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
                TextColumn::make('name')
                    ->label(__('cachet::incident.list.headers.name'))
                    ->searchable(),
                TextColumn::make('latest_status')
                    ->label(__('cachet::incident.list.headers.status'))
                    ->sortable()
                    ->badge(),
                TextColumn::make('visible')
                    ->label(__('cachet::incident.list.headers.visible'))
                    ->sortable()
                    ->badge(),
                IconColumn::make('stickied')
                    ->label(__('cachet::incident.list.headers.stickied'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                TextColumn::make('occurred_at')
                    ->label(__('cachet::incident.list.headers.occurred_at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('notifications')
                    ->label(__('cachet::incident.list.headers.notified_subscribers'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('cachet::incident.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label(__('cachet::incident.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('cachet::incident.list.headers.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->label(__('cachet::incident.list.headers.status'))
                    ->options(IncidentStatusEnum::class),
            ])
            ->recordActions([
                Action::make('add-update')
                    ->disabled(fn (Incident $record) => $record->status === IncidentStatusEnum::fixed)
                    ->label(__('cachet::incident.list.actions.record_update'))
                    ->color('info')
                    ->action(function (CreateIncidentUpdateAction $createIncidentUpdate, Incident $record, array $data) {
                        $createIncidentUpdate->handle($record, CreateIncidentUpdateRequestData::from($data));

                        Notification::make()
                            ->title(__('cachet::incident.record_update.success_title', ['name' => $record->name]))
                            ->body(__('cachet::incident.record_update.success_body'))
                            ->success()
                            ->send();
                    })
                    ->schema([
                        MarkdownEditor::make('message')
                            ->label(__('cachet::incident.record_update.form.message_label'))
                            ->required()
                            ->minHeight('200px')
                            ->maxHeight('300px')
                            ->columnSpanFull(),
                        ToggleButtons::make('status')
                            ->label(__('cachet::incident.record_update.form.status_label'))
                            ->options(IncidentStatusEnum::class)
                            ->inline()
                            ->required(),
                        Select::make('user_id')
                            ->label(__('cachet::incident.record_update.form.user_label'))
                            ->hint(__('cachet::incident.record_update.form.user_helper'))
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->searchable()
                            ->preload(),
                    ]),
                Action::make('view-incident')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Incident $record): string => route('cachet.status-page.incident', $record))
                    ->label(__('cachet::incident.list.actions.view_incident')),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('cachet::incident.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::incident.list.empty_state.description'));
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
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'edit' => EditIncident::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::incident.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::incident.resource_label', 2);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Incident::unresolved()
            ->get()
            ->filter(fn (Incident $incident) => in_array($incident->latest_status, IncidentStatusEnum::unresolved()))->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        if ((int) static::getNavigationBadge() > 0) {
            return 'danger';
        }

        return 'success';
    }
}
