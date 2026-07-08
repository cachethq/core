<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ComponentCheckFactory;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $component_id
 * @property ComponentStatusEnum $status
 * @property bool $successful
 * @property ?int $response_code
 * @property ?int $response_time
 * @property Carbon $checked_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Component $component
 *
 * @method static ComponentCheckFactory factory($count = null, $state = [])
 */
class ComponentCheck extends Model
{
    /** @use HasFactory<ComponentCheckFactory> */
    use HasFactory;

    use MassPrunable;

    /** @var array<string, string> */
    protected $casts = [
        'status' => ComponentStatusEnum::class,
        'successful' => 'bool',
        'response_code' => 'int',
        'response_time' => 'int',
        'checked_at' => 'datetime',
    ];

    /** @var list<string> */
    protected $fillable = [
        'component_id',
        'status',
        'successful',
        'response_code',
        'response_time',
        'checked_at',
    ];

    /**
     * Get the component the check belongs to.
     *
     * @return BelongsTo<Component, $this>
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the prunable model query.
     *
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<', now()->subDays(
            config('cachet.checks.prune_checks_after_days', 30)
        ));
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ComponentCheckFactory::new();
    }
}
