<?php

namespace Cachet\Mcp\Concerns;

use BackedEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Cachet\Models\Schedule;
use Cachet\Models\Subscriber;
use Cachet\Models\Update;
use UnitEnum;

trait PresentsResources
{
    /**
     * @return array{value: int|string, name: string}|null
     */
    protected function presentEnum(?UnitEnum $enum): ?array
    {
        if ($enum === null) {
            return null;
        }

        return [
            'value' => $enum instanceof BackedEnum ? $enum->value : $enum->name,
            'name' => $enum->name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentComponent(Component $component): array
    {
        return [
            'id' => $component->id,
            'name' => $component->name,
            'description' => $component->description,
            'link' => $component->link,
            'status' => $this->presentEnum($component->status),
            'latest_status' => $this->presentEnum($component->latest_status),
            'order' => $component->order,
            'enabled' => $component->enabled,
            'component_group_id' => $component->component_group_id,
            'tags' => $component->tags->pluck('name')->all(),
            'created_at' => $component->created_at?->toIso8601String(),
            'updated_at' => $component->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentComponentGroup(ComponentGroup $group): array
    {
        return array_merge([
            'id' => $group->id,
            'name' => $group->name,
            'order' => $group->order,
            'visible' => $this->presentEnum($group->visible),
            'collapsed' => $this->presentEnum($group->collapsed),
            'order_column' => $this->presentEnum($group->order_column),
            'order_direction' => $this->presentEnum($group->order_direction),
            'created_at' => $group->created_at?->toIso8601String(),
            'updated_at' => $group->updated_at?->toIso8601String(),
        ], $group->relationLoaded('components') ? [
            'components' => $group->components->map(fn (Component $component) => $this->presentComponent($component))->all(),
        ] : []);
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentIncident(Incident $incident): array
    {
        return array_merge([
            'id' => $incident->id,
            'guid' => $incident->guid,
            'name' => $incident->name,
            'status' => $this->presentEnum($incident->status),
            'message' => $incident->message,
            'visible' => $this->presentEnum($incident->visible),
            'stickied' => $incident->stickied,
            'tags' => $incident->tags->pluck('name')->all(),
            'occurred_at' => $incident->occurred_at?->toIso8601String(),
            'published_at' => $incident->published_at?->toIso8601String(),
            'created_at' => $incident->created_at?->toIso8601String(),
            'updated_at' => $incident->updated_at?->toIso8601String(),
        ], $incident->relationLoaded('components') ? [
            'components' => $incident->components->map(fn (Component $component) => $this->presentComponent($component))->all(),
        ] : [], $incident->relationLoaded('updates') ? [
            'updates' => $incident->updates->map(fn (Update $update) => $this->presentUpdate($update))->all(),
        ] : []);
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentUpdate(Update $update): array
    {
        return [
            'id' => $update->id,
            'status' => $this->presentEnum($update->status),
            'message' => $update->message,
            'user_id' => $update->user_id,
            'created_at' => $update->created_at?->toIso8601String(),
            'updated_at' => $update->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentIncidentTemplate(IncidentTemplate $template): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'slug' => $template->slug,
            'template' => $template->template,
            'engine' => $this->presentEnum($template->engine),
            'created_at' => $template->created_at?->toIso8601String(),
            'updated_at' => $template->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentSchedule(Schedule $schedule): array
    {
        return array_merge([
            'id' => $schedule->id,
            'name' => $schedule->name,
            'message' => $schedule->message,
            'status' => $this->presentEnum($schedule->status),
            'scheduled_at' => $schedule->scheduled_at?->toIso8601String(),
            'completed_at' => $schedule->completed_at?->toIso8601String(),
            'published_at' => $schedule->published_at?->toIso8601String(),
            'tags' => $schedule->tags->pluck('name')->all(),
            'created_at' => $schedule->created_at?->toIso8601String(),
            'updated_at' => $schedule->updated_at?->toIso8601String(),
        ], $schedule->relationLoaded('components') ? [
            'components' => $schedule->components->map(fn (Component $component) => $this->presentComponent($component))->all(),
        ] : [], $schedule->relationLoaded('updates') ? [
            'updates' => $schedule->updates->map(fn (Update $update) => $this->presentUpdate($update))->all(),
        ] : []);
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentMetric(Metric $metric): array
    {
        return array_merge([
            'id' => $metric->id,
            'name' => $metric->name,
            'suffix' => $metric->suffix,
            'description' => $metric->description,
            'calc_type' => $this->presentEnum($metric->calc_type),
            'display_chart' => $metric->display_chart,
            'places' => $metric->places,
            'default_value' => $metric->default_value,
            'threshold' => $metric->threshold,
            'visible' => $this->presentEnum($metric->visible),
            'order' => $metric->order,
            'tags' => $metric->tags->pluck('name')->all(),
            'created_at' => $metric->created_at?->toIso8601String(),
            'updated_at' => $metric->updated_at?->toIso8601String(),
        ], $metric->relationLoaded('metricPoints') ? [
            'metric_points' => $metric->metricPoints->map(fn (MetricPoint $point) => $this->presentMetricPoint($point))->all(),
        ] : []);
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentMetricPoint(MetricPoint $point): array
    {
        return [
            'id' => $point->id,
            'metric_id' => $point->metric_id,
            'value' => $point->value,
            'counter' => $point->counter,
            'calculated_value' => $point->calculated_value,
            'created_at' => $point->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function presentSubscriber(Subscriber $subscriber): array
    {
        return array_merge([
            'id' => $subscriber->id,
            'email' => $subscriber->email,
            'verified' => $subscriber->hasVerifiedEmail(),
            'global' => (bool) $subscriber->global,
            'created_at' => $subscriber->created_at?->toIso8601String(),
            'updated_at' => $subscriber->updated_at?->toIso8601String(),
        ], $subscriber->relationLoaded('components') ? [
            'components' => $subscriber->components->map(fn (Component $component) => $this->presentComponent($component))->all(),
        ] : []);
    }
}
