<?php

namespace Cachet\Filament\Widgets;

use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Filament\Widgets\Concerns\PollsFromAppSettings;
use Cachet\Models\Incident;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OpenIncidents extends TableWidget
{
    use PollsFromAppSettings;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Incident::query()
                    ->unresolved()
                    ->withCount('components')
                    ->orderByDesc('occurred_at')
            )
            ->heading(__('cachet::dashboard.open_incidents.heading'))
            ->poll($this->getPollingInterval())
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::dashboard.open_incidents.headers.name')),
                TextColumn::make('latest_status')
                    ->label(__('cachet::dashboard.open_incidents.headers.status'))
                    ->badge(),
                TextColumn::make('occurred_at')
                    ->label(__('cachet::dashboard.open_incidents.headers.occurred_at'))
                    ->since()
                    ->tooltip(fn (Incident $record): string => $record->timestamp->toDayDateTimeString()),
                TextColumn::make('components_count')
                    ->label(__('cachet::dashboard.open_incidents.headers.components')),
            ])
            ->recordUrl(fn (Incident $record): string => $record->filamentDashboardEditUrl())
            ->headerActions([
                Action::make('create')
                    ->label(__('cachet::dashboard.open_incidents.actions.create'))
                    ->url(IncidentResource::getUrl('create')),
            ])
            ->emptyStateHeading(__('cachet::dashboard.open_incidents.empty_state.heading'))
            ->emptyStateDescription(__('cachet::dashboard.open_incidents.empty_state.description'))
            ->emptyStateIcon('cachet-circle-check');
    }
}
