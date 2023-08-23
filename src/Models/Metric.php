<?php

namespace Cachet\Models;

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Metrics\MetricCreated;
use Cachet\Events\Metrics\MetricDeleted;
use Cachet\Events\Metrics\MetricUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the metrics points.
     */
    public function points(): HasMany
    {
        return $this->hasMany(MetricPoint::class);
    }
}
