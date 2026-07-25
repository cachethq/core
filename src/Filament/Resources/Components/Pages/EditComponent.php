<?php

namespace Cachet\Filament\Resources\Components\Pages;

use Cachet\Actions\Component\ChangeComponentStatus;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\Components\ComponentResource;
use Cachet\Models\Component;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditComponent extends EditRecord
{
    use InteractsWithMeta;

    protected static string $resource = ComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillMetaFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractMetaFormData($data);
    }

    /**
     * Route a status change through the action so it is recorded and announced
     * like every other status change, rather than being saved straight to the
     * column by the form.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $status = $data['status'] ?? null;

        unset($data['status']);

        $record = parent::handleRecordUpdate($record, $data);

        if ($status !== null && $record instanceof Component) {
            app(ChangeComponentStatus::class)->handle(
                $record,
                $status instanceof ComponentStatusEnum ? $status : ComponentStatusEnum::from((int) $status),
                ComponentStatusSourceEnum::Manual,
                auth()->user(),
            );
        }

        return $record;
    }

    protected function afterSave(): void
    {
        $this->persistMeta();
    }
}
