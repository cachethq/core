<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentUpdateFactory;
use Cachet\Enums\IncidentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Properties
 *
 * @property-read int $id
 * @property int $user_id
 * @property IncidentStatusEnum $status
 * @property string $message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Incident $incident
 *
 * Methods
 *
 * @method static IncidentUpdateFactory factory($count = null, $state = [])
 */
class IncidentUpdate extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => IncidentStatusEnum::class,
    ];

    protected $fillable = [
        'status',
        'message',
        'user_id',
    ];

    /**
     * Get the incident that the update belongs to.
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
