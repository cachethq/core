<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\MetricPointFactory;
use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Events\Metrics\MetricPointDeleted;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Properties
 *
 * @property-read int $id
 * @property int $metric_id
 * @property float $value
 * @property int $counter
 * @property int|float $calculated_value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Metric $metric
 *
 * Methods
 *
 * @method static  MetricPointFactory factory($count = null, $state = [])
 */
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
     * Override the created_at column to round the value into a 30-second interval.
     */
    public function createdAt(): Attribute
    {
        return Attribute::make(
            set: function ($createdAt): ?Carbon {
                if (! $createdAt) {
                    return null;
                }

                if (! $createdAt instanceof DateTime) {
                    $createdAt = Carbon::parse($createdAt);
                }

                $timestamp = $createdAt->unix();
                $timestamp = 30 * round($timestamp / 30);

                return Carbon::createFromTimestamp($timestamp);
            }
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
    public function withinThreshold(int $threshold, string|int|DateTime $timestamp = null): bool
    {
        $now = $timestamp ? Carbon::parse($timestamp) : now();

        return $this->created_at->startOfMinute()->diffInMinutes($now->startOfMinute()) < $threshold;
    }
}
