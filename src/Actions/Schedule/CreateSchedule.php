<?php

declare(strict_types=1);

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSchedule
{
    use AsAction;

    public function handle(array $data, ?array $components = []): Schedule
    {
        return tap(Schedule::create($data), function (Schedule $schedule) use ($components) {
            if (! $components) {
                return;
            }

            $components = collect($components)->map(fn ($component) => [
                'component_id' => $component['id'],
                'component_status' => $component['status'],
            ])->all();

            $schedule->components()->sync($components);
        });
    }
}
