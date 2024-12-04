<?php

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IncidentComponent extends Pivot
{
    use HasFactory;

    protected $casts = [
        'status' => ComponentStatusEnum::class,
    ];

    /**
     * Get the incident the component is attached to.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the incident the component is attached to.
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return IncidentComponentFactory::new();
    }
}
