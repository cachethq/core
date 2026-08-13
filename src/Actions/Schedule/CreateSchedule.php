<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Data\Requests\Schedule\ScheduleComponentRequestData;
use Cachet\Models\Schedule;
use Illuminate\Support\Facades\DB;

class CreateSchedule
{
    public function __construct(private NotifyScheduleSubscribers $notifyScheduleSubscribers)
    {
        //
    }

    /**
     * Handle the action.
     */
    public function handle(CreateScheduleRequestData $data): Schedule
    {
        /** @phpstan-ignore-next-line argument.type */
        $schedule = DB::transaction(function () use ($data): Schedule {
            return tap(Schedule::create($data->except('components', 'meta', 'tags')->toArray()), function (Schedule $schedule) use ($data) {
                if ($data->components) {
                    $components = collect($data->components)
                        ->mapWithKeys(fn (ScheduleComponentRequestData $component) => [
                            $component->id => ['component_status' => $component->status],
                        ])
                        ->all();

                    $schedule->components()->sync($components);
                }

                $schedule->syncMeta($data->meta ?? []);
                $schedule->syncTags($data->tags);
            });
        });

        $this->notifyScheduleSubscribers->handle($schedule);

        return $schedule;
    }
}
