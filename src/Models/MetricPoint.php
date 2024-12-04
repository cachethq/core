<?php

namespace Cachet\Models;

use Cachet\Database\Factories\MetricPointFactory;
use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Events\Metrics\MetricPointDeleted;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $metric_id
 * @property float $value
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int $counter
 * @property Metric $metric
 * @property-read float $calculated_value
 *
 * @method static MetricPointFactory factory($count = null, $state = [])
 */
class MetricPoint extends Model
{
    /** @use HasFactory<MetricPointFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'float',
    ];

    /** @var array<string, string> */
    protected $dispatchesEvents = [
        'created' => MetricPointCreated::class,
        'deleted' => MetricPointDeleted::class,
    ];

    /** @var list<string> */
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
            set: function ($createdAt) {
                if (! $createdAt) {
                    return;
                }

                if (! $createdAt instanceof DateTime) {
                    $createdAt = Carbon::parse($createdAt);
                }

                $timestamp = $createdAt->getTimestamp();
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
    public function withinThreshold(int $threshold, string|int|DateTime|null $timestamp = null): bool
    {
        if (blank($timestamp)) {
            $now = now();
        }

        $now ??= Carbon::parse($timestamp);

        return $this->created_at->startOfMinute()->diffInMinutes($now->startOfMinute()) < $threshold;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MetricPointFactory::new();
    }
}
