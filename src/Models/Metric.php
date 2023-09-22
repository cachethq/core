<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\MetricFactory;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Metrics\MetricCreated;
use Cachet\Events\Metrics\MetricDeleted;
use Cachet\Events\Metrics\MetricUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Properties
 *
 * @property-read int $id
 * @property string $name
 * @property string $suffix
 * @property string $description
 * @property int $status
 * @property string $default_value
 * @property int $threshold
 * @property MetricTypeEnum $calc_type
 * @property bool $display_chart
 * @property int $places
 * @property MetricViewEnum $default_view
 * @property ResourceVisibilityEnum $visible
 * @property int $order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<array-key, MetricPoint> $metricPoints
 * @property Collection<array-key, MetricPoint> $recentMetricPoints
 *
 * Methods
 *
 * @method static MetricFactory factory($count = null, $state = [])
 */
class Metric extends Model
{
    use HasFactory;

    protected $casts = [
        'calc_type' => MetricTypeEnum::class,
        'display_chart' => 'bool',
        'places' => 'int',
        'default_view' => MetricViewEnum::class,
        'visible' => ResourceVisibilityEnum::class,
        'order' => 'int',
    ];

    protected $dispatchesEvents = [
        'created' => MetricCreated::class,
        'deleted' => MetricDeleted::class,
        'updated' => MetricUpdated::class,
    ];

    protected $fillable = [
        'name',
        'suffix',
        'description',
        'calc_type',
        'display_chart',
        'places',
        'default_value',
        'default_view',
        'visible',
        'order',
    ];

    /**
     * Get the metrics points.
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
}
