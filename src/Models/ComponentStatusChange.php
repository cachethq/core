<?php

namespace Cachet\Models;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * A record of a change to a component's baseline status, and what caused it.
 *
 * @property int $id
 * @property int $component_id
 * @property ?ComponentStatusEnum $old_status
 * @property ComponentStatusEnum $new_status
 * @property ComponentStatusSourceEnum $source
 * @property ?string $reason
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Component $component
 */
class ComponentStatusChange extends Model
{
    /** @var array<string, string> */
    protected $casts = [
        'old_status' => ComponentStatusEnum::class,
        'new_status' => ComponentStatusEnum::class,
        'source' => ComponentStatusSourceEnum::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'component_id',
        'old_status',
        'new_status',
        'source',
        'reason',
        'causer_type',
        'causer_id',
    ];

    /**
     * Get the component whose status changed.
     *
     * @return BelongsTo<Component, $this>
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get whoever or whatever caused the change.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
