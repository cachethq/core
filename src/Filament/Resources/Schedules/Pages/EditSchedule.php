<?php

namespace Cachet\Filament\Resources\Schedules\Pages;

use Cachet\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ScheduleResource::recordUpdateAction()
                ->label(__('cachet::schedule.add_update.new_update_label'))
                ->icon('heroicon-o-plus'),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['completed_at'] = $data['completed_at'] ?? null;

        return $data;
    }
}
