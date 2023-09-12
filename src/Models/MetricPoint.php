<?php

namespace Cachet\Models;

use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Events\Metrics\MetricPointDeleted;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
    public function withinThreshold(int $threshold, string|int|null|DateTime $timestamp = null): bool
    {
        $now = Carbon::parse($timestamp) ?? now();

        return $this->created_at->startOfMinute()->diffInMinutes($now->startOfMinute()) < $threshold;
    }
}
