<?php

namespace Cachet\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Resources\Components\ComponentResource;
use Cachet\Filament\Widgets\Concerns\PollsFromAppSettings;
use Cachet\Status;
use Filament\Widgets\Widget;

class SystemHealth extends Widget
{
    use PollsFromAppSettings;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'cachet::filament.widgets.system-health';

    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        $status = app(Status::class);
        $components = $status->components();

        return [
            'systemStatus' => $status->current(),
            'totalComponents' => (int) $components->total,
            'statusCounts' => $this->statusCounts($components),
            'createComponentUrl' => ComponentResource::getUrl('create'),
            'pollingInterval' => $this->getPollingInterval(),
        ];
    }

    /**
     * Get the number of components per status, excluding statuses with no components.
     *
     * @return array<int, array{status: ComponentStatusEnum, count: int}>
     */
    protected function statusCounts(object $components): array
    {
        return collect([
            ComponentStatusEnum::operational,
            ComponentStatusEnum::performance_issues,
            ComponentStatusEnum::partial_outage,
            ComponentStatusEnum::major_outage,
            ComponentStatusEnum::under_maintenance,
        ])
            ->map(fn (ComponentStatusEnum $status) => [
                'status' => $status,
                'count' => (int) $components->{$status->name},
            ])
            ->filter(fn (array $item) => $item['count'] > 0)
            ->values()
            ->all();
    }
}
