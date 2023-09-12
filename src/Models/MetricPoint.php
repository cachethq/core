<?php

namespace Cachet\Models;

use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Events\Metrics\MetricPointDeleted;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricPoint extends Model
{
    use HasFactory;

    protected $casts = [
        'value' => 'float',
    ];

    protected $dispatchesEvents = [
        'created' => MetricPointCreated::class,
        'deleted' => MetricPointDeleted::class,
    ];

    protected $fillable = [
        'value',
        'counter',
    ];

    public function calculatedValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->value * $this->counter
        );
    }

    /**
     * Get the metric the point belongs to.
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class);
    }

    /**
     * Determine if the metric point was created within the threshold.
     */
    public function withinThreshold(int $threshold): bool
    {
        return $this->created_at->diffInMinutes(now()->startOfMinute()) < $threshold;
    }
}
