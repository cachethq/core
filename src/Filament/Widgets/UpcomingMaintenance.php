<?php

namespace Cachet\Filament\Widgets;

use Cachet\Filament\Resources\Schedules\ScheduleResource;
use Cachet\Models\Schedule;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingMaintenance extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Schedule::query()
                    ->incomplete()
                    ->orderBy('scheduled_at')
            )
            ->heading(__('cachet::dashboard.upcoming_maintenance.heading'))
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::dashboard.upcoming_maintenance.headers.name')),
                TextColumn::make('status')
                    ->label(__('cachet::dashboard.upcoming_maintenance.headers.status'))
                    ->badge(),
                TextColumn::make('scheduled_at')
                    ->label(__('cachet::dashboard.upcoming_maintenance.headers.scheduled_at'))
                    ->since()
                    ->tooltip(fn (Schedule $record): string => $record->scheduled_at->toDayDateTimeString()),
            ])
            ->recordUrl(fn (Schedule $record): string => ScheduleResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Action::make('create')
                    ->label(__('cachet::dashboard.upcoming_maintenance.actions.create'))
                    ->url(ScheduleResource::getUrl('create')),
            ])
            ->emptyStateHeading(__('cachet::dashboard.upcoming_maintenance.empty_state.heading'))
            ->emptyStateDescription(__('cachet::dashboard.upcoming_maintenance.empty_state.description'))
            ->emptyStateIcon('cachet-clock');
    }
}
