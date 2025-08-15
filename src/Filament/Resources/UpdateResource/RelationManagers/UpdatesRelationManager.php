<?php

namespace Cachet\Filament\Resources\UpdateResource\RelationManagers;

use Cachet\Actions\Incident\SetLatestStatusIncident;
use Cachet\Actions\Update\CreateUpdate as CreateUpdateAction;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use PHPUnit\Runner\ErrorException;

class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\MarkdownEditor::make('message')
                    ->label(__('Message'))
                    ->required()
                    ->minHeight('200px')
                    ->maxHeight('300px')
                    ->columnSpanFull(),
                Forms\Components\Select::make('user_id')
                    ->label(__('User'))
                    ->hint(__('The user who reported the incident.'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label(__('Password'))
                            ->required()
                            ->password()
                            ->confirmed()
                            ->minLength(8),
                    ]),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('Status'))
                    ->inline()
                    ->columnSpanFull()
                    ->options(IncidentStatusEnum::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modelLabel(__('Update'))
            ->pluralModelLabel(__('Updates'))
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(IncidentStatusEnum::class),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->action(function (CreateUpdateAction $createUpdate, array $data) {
                        /** @var Schedule|Incident $resource */
                        $resource = $this->getOwnerRecord();
                        $requestData = match (get_class($resource)) {
                            Schedule::class => CreateScheduleUpdateRequestData::from($data),
                            Incident::class => CreateIncidentUpdateRequestData::from($data),
                            default => throw new ErrorException('Request data resource mismatch')
                        };

                        $createUpdate->handle($resource, $requestData);

                        if($resource instanceof Incident) {
                            Notification::make()
                                ->title(__('cachet::incident.record_update.success_title', ['name' => $resource->name]))
                                ->body(__('cachet::incident.record_update.success_body'))
                                ->success()
                                ->send();

                            // Somehow the events aren't working to update the parent
                            // So then let's just refresh the page
                            redirect(request()->header('Referer'));
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function(SetLatestStatusIncident $latestStatusIncident) {
                        $this->afterChanged($latestStatusIncident);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function(SetLatestStatusIncident $latestStatusIncident) {
                        $this->afterChanged($latestStatusIncident);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->after(function(SetLatestStatusIncident $latestStatusIncident) {
                        $this->afterChanged($latestStatusIncident);
                    }),
                ]),
            ]);
    }

    protected function afterChanged(?SetLatestStatusIncident $latestStatusIncident = null): void
    {
        /** @var Schedule|Incident $resource */
        $resource = $this->getOwnerRecord();
        if(is_null($latestStatusIncident)) {
            $latestStatusIncident = app()->make(SetLatestStatusIncident::class);
        }

        if ($resource instanceof Incident) {
            $latestStatusIncident->handle($resource);

            // Somehow the events aren't working to update the parent
            // So then let's just refresh the page
            redirect(request()->header('Referer'));
        }

    }
}
