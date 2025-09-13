<?php

namespace Cachet\Models;

use Cachet\Concerns\HasVisibility;
use Cachet\Database\Factories\MetricFactory;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Metrics\MetricCreated;
use Cachet\Events\Metrics\MetricDeleted;
use Cachet\Events\Metrics\MetricUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $suffix
 * @property ?string $description
 * @property ?numeric $default_value
 * @property MetricTypeEnum $calc_type
 * @property int $display_chart
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int $places
 * @property MetricViewEnum $default_view
 * @property int $threshold
 * @property int $order
 * @property ResourceVisibilityEnum $visible
 * @property Collection<int, MetricPoint> $metricPoints
 * @property Collection<int, MetricPoint> $recentMetricPoints
 *
 * @method static MetricFactory factory($count = null, $state = [])
 */
class Metric extends Model
{
    /** @use HasFactory<MetricFactory> */
    use HasFactory;

    use HasVisibility;

    /** @var array<string, string> */
    protected $casts = [
        'calc_type' => MetricTypeEnum::class,
        'display_chart' => 'bool',
        'show_when_empty' => 'bool',
        'places' => 'int',
        'default_view' => MetricViewEnum::class,
        'visible' => ResourceVisibilityEnum::class,
        'order' => 'int',
    ];

    /** @var array<string, string> */
    protected $dispatchesEvents = [
        'created' => MetricCreated::class,
        'deleted' => MetricDeleted::class,
        'updated' => MetricUpdated::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'suffix',
        'description',
        'calc_type',
        'display_chart',
        'show_when_empty',
        'places',
        'default_value',
        'default_view',
        'visible',
        'order',
    ];

    /**
     * Get the metrics points.
     *
     * @return HasMany<MetricPoint, $this>
     */
    public function metricPoints(): HasMany
    {
        return $this->hasMany(MetricPoint::class);
    }

    /**
     * Get the most recent metric points.
     */
    public function recentMetricPoints(int $points = 15): HasMany
    {
        return $this->metricPoints()->latest()->limit($points);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MetricFactory::new();
    }
}
