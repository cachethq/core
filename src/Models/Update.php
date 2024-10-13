<?php

namespace Cachet\Models;

use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Update extends Model
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
}
