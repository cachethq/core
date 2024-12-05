<?php

namespace Cachet\Models;

use Cachet\Database\Factories\UpdateFactory;
use Cachet\Enums\IncidentStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * @template TUser of Authenticatable
 *
 * @property int $id
 * @property ?IncidentStatusEnum $status
 * @property string $message
 * @property ?int $user_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property class-string<Incident|Schedule> $updateable_type
 * @property int $updateable_id
 * @property Incident|Schedule $updateable
 * @property TUser $user
 *
 * @method static UpdateFactory factory($count = null, $state = [])
 */
class Update extends Model
{
    /** @use HasFactory<UpdateFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'status' => IncidentStatusEnum::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'status',
        'message',
        'user_id',
    ];

    /**
     * Get the resource that the update belongs to.
     */
    public function updateable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('cachet.user_model'));
    }

    /**
     * Render the Markdown message.
     */
    public function formattedMessage(): string
    {
        return Str::of($this->message)->markdown();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UpdateFactory::new();
    }
}
