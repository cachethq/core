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
                        ->label(__('cachet::incident.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),
                    Forms\Components\ToggleButtons::make('status')
                        ->label(__('cachet::incident.form.name_label'))
                        ->inline()
                        ->columnSpanFull()
                        ->options(IncidentStatusEnum::class)
                        ->required(),
                    Forms\Components\MarkdownEditor::make('message')
                        ->label(__('cachet::incident.form.message_label'))
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('occurred_at')
                        ->label(__('cachet::incident.form.occurred_at_label'))
                        ->helperText(__('cachet::incident.form.occurred_at_helper')),
                    Forms\Components\ToggleButtons::make('visible')
                        ->label(__('cachet::incident.form.visible_label'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Forms\Components\Repeater::make('incidentComponents')
                        ->visibleOn('create')
                        ->relationship()
                        ->defaultItems(0)
                        ->addActionLabel(__('cachet::incident.form.add_component.action_label'))
                        ->schema([
                            Forms\Components\Select::make('component_id')
                                ->preload()
                                ->required()
                                ->relationship('component', 'name')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->label(__('cachet::incident.form.add_component.component_label')),
                            Forms\Components\ToggleButtons::make('component_status')
                                ->label(__('cachet::incident.form.add_component.status_label'))
                                ->inline()
                                ->options(ComponentStatusEnum::class)
                                ->required(),
                        ])
                        ->label(__('cachet::incident.form.add_component.header')),
                ])
                    ->columnSpan(3),
                Section::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->label(__('User'))
                        ->helperText(__('cachet::incident.form.user_helper'))
                        ->relationship('user', 'name')
                        ->default(auth()->id())
                        ->searchable()
                        ->preload(),
                    Forms\Components\Toggle::make('notifications')
                        ->label(__('cachet::incident.form.notifications_label'))
                        ->required(),
                    Forms\Components\Toggle::make('stickied')
                        ->label(__('cachet::incident.form.stickied_label'))
                        ->required(),
                    Forms\Components\TextInput::make('guid')
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('cachet::incident.list.headers.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('cachet::incident.list.headers.status'))
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('visible')
                    ->label(__('cachet::incident.list.headers.visible'))
                    ->sortable()
                    ->badge(),
                Tables\Columns\IconColumn::make('stickied')
                    ->label(__('cachet::incident.list.headers.stickied'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label(__('cachet::incident.list.headers.occurred_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('notifications')
                    ->label(__('cachet::incident.list.headers.notified_subscribers'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cachet::incident.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('cachet::incident.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('cachet::incident.list.headers.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('cachet::incident.list.headers.status'))
                    ->options(IncidentStatusEnum::class),
            ])
            ->actions([
                Action::make('add-update')
                    ->disabled(fn (Incident $record) => $record->status === IncidentStatusEnum::fixed)
                    ->label(__('cachet::incident.list.actions.record_update'))
                    ->color('info')
                    ->action(function (CreateIncidentUpdateAction $createIncidentUpdate, Incident $record, array $data) {
                        $createIncidentUpdate->handle($record, CreateIncidentUpdateData::from($data));

                        Notification::make()
                            ->title(__('cachet::incident.record_update.success_title', ['name' => $record->name]))
                            ->body(__('cachet::incident.record_update.success_body'))
                            ->success()
                            ->send();
                    })
                    ->form([
                        Forms\Components\MarkdownEditor::make('message')
                            ->label(__('cachet::incident.record_update.form.message_label'))
                            ->required(),
                        Forms\Components\ToggleButtons::make('status')
                            ->label(__('cachet::incident.record_update.form.status_label'))
                            ->options(IncidentStatusEnum::class)
                            ->inline()
                            ->required(),
                        Forms\Components\Select::make('user_id')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
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
