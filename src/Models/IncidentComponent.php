<?php

namespace Cachet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentComponent extends Model
{
    use HasFactory;

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
}
