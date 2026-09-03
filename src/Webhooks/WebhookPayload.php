<?php

namespace Cachet\Webhooks;

use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Cachet\Models\Subscriber;
use Illuminate\Support\Carbon;

final class WebhookPayload
{
    public static function component(Component $component): array
    {
        return self::resource($component->id, 'component', [
            'name' => $component->name,
            'description' => $component->description,
            'link' => $component->link,
            'order' => $component->order,
            'status' => $component->status->value,
            'group_id' => $component->component_group_id,
            'enabled' => $component->enabled,
            'checked' => $component->checked,
            'checked_at' => self::timestamp($component->getAttribute('checked_at')),
            'created_at' => self::timestamp($component->created_at),
            'updated_at' => self::timestamp($component->updated_at),
        ]);
    }

    public static function incident(Incident $incident): array
    {
        return self::resource($incident->id, 'incident', [
            'guid' => $incident->guid,
            'name' => $incident->name,
            'message' => $incident->message,
            'status' => $incident->status?->value,
            'visibility' => $incident->visible->value,
            'stickied' => $incident->stickied,
            'notifications' => (bool) $incident->notifications,
            'user_id' => $incident->user_id,
            'external_provider' => $incident->external_provider,
            'external_id' => $incident->external_id,
            'occurred_at' => self::timestamp($incident->occurred_at),
            'published_at' => self::timestamp($incident->published_at),
            'created_at' => self::timestamp($incident->created_at),
            'updated_at' => self::timestamp($incident->updated_at),
        ]);
    }

    public static function metric(Metric $metric): array
    {
        return self::resource($metric->id, 'metric', [
            'name' => $metric->name,
            'suffix' => $metric->suffix,
            'description' => $metric->description,
            'default_value' => $metric->default_value,
            'calculation_type' => $metric->calc_type->value,
            'display_chart' => (bool) $metric->display_chart,
            'show_when_empty' => (bool) $metric->show_when_empty,
            'decimal_places' => $metric->places,
            'default_view' => $metric->default_view->value,
            'threshold' => $metric->threshold,
            'order' => $metric->order,
            'visibility' => $metric->visible->value,
            'created_at' => self::timestamp($metric->created_at),
            'updated_at' => self::timestamp($metric->updated_at),
        ]);
    }

    public static function metricPoint(MetricPoint $metricPoint): array
    {
        return self::resource($metricPoint->id, 'metric_point', [
            'metric_id' => $metricPoint->metric_id,
            'value' => $metricPoint->value,
            'counter' => $metricPoint->counter,
            'calculated_value' => $metricPoint->calculated_value,
            'created_at' => self::timestamp($metricPoint->created_at),
            'updated_at' => self::timestamp($metricPoint->updated_at),
        ]);
    }

    public static function subscriber(Subscriber $subscriber): array
    {
        return self::resource($subscriber->id, 'subscriber', [
            'email' => $subscriber->email,
            'global' => (bool) $subscriber->global,
            'verified_at' => self::timestamp($subscriber->email_verified_at),
            'created_at' => self::timestamp($subscriber->created_at),
            'updated_at' => self::timestamp($subscriber->updated_at),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{resource: string, id: int, attributes: array<string, mixed>}
     */
    private static function resource(int $id, string $resource, array $attributes): array
    {
        return [
            'resource' => $resource,
            'id' => $id,
            'attributes' => $attributes,
        ];
    }

    private static function timestamp(mixed $timestamp): ?string
    {
        return $timestamp === null ? null : Carbon::parse($timestamp)->toISOString();
    }
}
