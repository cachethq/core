<?php

declare(strict_types=1);

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSchedule
{
    use AsAction;

    public function handle(Schedule $schedule, array $data = [], ?array $components = []): Schedule
    {
        $schedule->update($data);

        if ($components) {
            $components = collect($components)->map(fn ($component) => [
                'component_id' => $component['id'],
                'component_status' => $component['status'],
            ])->all();

            $schedule->components()->sync($components);
        }

        return $schedule->fresh();
    }
}
